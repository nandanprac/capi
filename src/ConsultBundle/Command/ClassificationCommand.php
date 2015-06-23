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
class ClassificationCommand extends ContainerAwareCommand
{
    private $container;
    private $queue;
    private $classification;

    /**
     * Initialize Connections
     * @param InputInterface  $input  input
     * @param OutputInterface $output output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->container = $this->getContainer();
        $this->queue = $this->container->get('consult.consult_queue');
        $this->classification = $this->container->get('consult.classification');
    }

    /**
     * Configure the task with options and arguments
     */
    protected function configure()
    {
        $this->setName('consult:question:classification:queue')
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
                ->setQueueName(Queue::CLASSIFY)
                ->receiveMessage();
            if ($newJob) {
                $jobData = json_decode($newJob, true);
                try {
                    $subject = array_key_exists('subject', $jobData) ? $jobData['subject'] : null;
                    $question = array_key_exists('question', $jobData) ? $jobData['question'] : null;
                    $userSpeciality = array_key_exists('speciality', $jobData) ? $jobData['speciality'] : null;
                    $speciality = '';
                    $jobData['tags'] = $this->classification->sentenceWords($subject.' '.$question);
                    if (!$userSpeciality) {
                        $speciality = $this->classification->classifi($subject.' '.$question);
                        $jobData['user_classified'] = 0;
                        if (!$speciality) {
                            $jobData['algo_classified'] = 0;
                            $speciality = 'General Physician';
                        } else {
                            $jobData['algo_classified'] = 1;
                        }
                    } else {
                        $speciality = $userSpeciality;
                        $jobData['user_classified'] = 1;
                        $jobData['algo_classified'] = -1;
                    }
                    $jobData['speciality'] = $speciality;
                    unset($jobData['question']);
                    $output->writeln(json_encode($jobData));
                    $this->queue
                        ->setQueueName(Queue::DAA)
                        ->sendMessage(json_encode($jobData));
                } catch (\Exception $e) {
                    $output->writeln($e->getMessage());
                    $output->writeln($newJob);
                }
                $this->queue->setQueueName(Queue::CLASSIFY)->deleteMessage($newJob);
            }
        }
    }
}
