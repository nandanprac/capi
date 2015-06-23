<?php

namespace ConsultBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpFoundation\Request;
use ConsultBundle\Queue\AbstractQueue as Queue;
use ConsultBundle\Constants\ConsultFeatureData;
use ConsultBundle\ConsultDomain;
use Elasticsearch;

/**
 * Command to merge the accounts and Make the necessary updates.
 */
class DoctorAssigmentCommand extends ContainerAwareCommand
{

    private $queue;

    private $daaDebug;

    private $fabricSearch;

    private $client;

    /**
     * Initialize Connections
     * @param InputInterface  $input  input
     * @param OutputInterface $output output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        //$this->container = $this->getContainer();
        $this->queue = $this->getContainer()->get('consult.consult_queue');
        $this->daaDebug = $this->getContainer()->getParameter('daa_debug');
        $this->fabricSearch = $this->getContainer()->getParameter('elastic_index_name');
        $hosts = $this->getContainer()->getParameter('elasticsearch_hosts');
        if (empty($hosts)) {
            $hosts = 'localhost:9200';
        }

        $params['hosts'] = array($hosts);
        $this->client = new Elasticsearch\Client($params);
    }

    /**
     * Configure the task with options and arguments
     */
    protected function configure()
    {
        $this->setName('consult:question:doctorassignment:queue')
            ->setDescription('Queue to assign new questions to doctors')
            ->addArgument('domain', InputArgument::OPTIONAL, 'Consult Domain', 'https://consult.practo.com');
    }
    /**
     * Command executes logic
     *
     * @param InputInterface  $input  - InputInterface
     * @param OutputInterface $output - OutputInterface
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = Request::create($input->getArgument('domain'));
        $consultDomain = new ConsultDomain($request);
        $this->container->set('consult.consult_domain', $consultDomain);
        $this->queue->setConsultDomain($consultDomain);
        while (1) {
            $newJob = $this->queue
                ->setQueueName(Queue::DAA)
                ->receiveMessage();
            if ($newJob) {
                $jobData = json_decode($newJob, true);
                try {
                    // Question State Creation
                    if ($jobData['speciality'] and $jobData['speciality'] != 'GENERIC') {
                        $state = 'ASSIGNED';
                    } elseif ($jobData['speciality'] == 'GENERIC') {
                        $state = 'GENERIC';
                    }

                    // Job preparation
                    $jobData['question_id'] = array_key_exists('question_id', $jobData) ? $jobData['question_id'] : null;
                    if ($state == 'ASSIGNED') {
                        $city = array_key_exists('city', $jobData) ? $jobData['city'] : null;
                        if (!$city) {
                            $city = "bangalore";
                        }

                        $params['index'] = $this->fabricSearch;
                        $params['type']  = 'search';
                        $params['_source']  = array('practo_account_id', 'doctor_name');
                        $params['body']['query']['bool']['must'] = array(
                            array("query_string"=>array("default_field"=>"search.city", "query"=> $city)),
                            array("query_string"=>array("default_field"=>"search.specialties.specialty", "query"=> $jobData['speciality'])),
                        );
                        $params['body']['from']  = 0;
                        $params['body']['size']  = 100;
                        $results = $this->client->search($params);
                        $doctorIds = array();
                        foreach ($results['hits']['hits'] as $result) {
                            if (array_key_exists("practo_account_id", $result["_source"]) and $result["_source"]["practo_account_id"] != null) {
                                array_push($doctorIds, $result["_source"]["practo_account_id"]);
                            }
                        }
                        if ($doctorIds) {
                            $jobData['state'] = $state;
                            shuffle($doctorIds);
                            $jobData['doctors'] = array_unique(array_merge(array_slice($doctorIds, 0, 3), array()));
                        } else {
                            $jobData['state'] = 'DOCNOTFOUND';
                            $jobData['doctors'] = null;
                        }
                    } elseif ($state = 'GENERIC') {
                        $jobData['state'] = $state;
                        $jobData['doctors'] = null;
                    }
                    $jobData['send_to'] = 'synapse';
                    $output->writeln(json_encode($jobData));
                    $this->queue
                        ->setQueueName(Queue::ASSIGNMENT_UPDATE)
                        ->sendMessage(json_encode($jobData));
                } catch (\Exception $e) {
                    $output->writeln($e->getMessage());
                    $output->writeln($newJob);
                }
                $this->queue->setQueueName(Queue::DAA)->deleteMessage($newJob);
            }
        }
    }
}
