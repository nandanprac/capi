<?php

namespace ConsultBundle\Command;

use ConsultBundle\Manager\ClassificationManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpFoundation\Request;
use ConsultBundle\Queue\AbstractQueue as Queue;
use ConsultBundle\Constants\ConsultFeatureData;
use ConsultBundle\ConsultDomain;

/**
 * Command to merge the accounts and Make the necessary updates.
 */
class ClassificationCommand extends ContainerAwareCommand
{
    private $container;
    private $queue;
    /**
     * @var ClassificationManager $classification
     */
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
                if (!isset($this->classification)) {
                    $this->classification = $this->container->get('consult.classification');
                }
                try {
                    $subject = array_key_exists('subject', $jobData) ? $jobData['subject'] : null;
                    $question = array_key_exists('question', $jobData) ? $jobData['question'] : null;
                    $userSpeciality = array_key_exists('speciality', $jobData) ? $jobData['speciality'] : null;
                    $questionId = array_key_exists('question_id', $jobData) ? $jobData['question_id'] : null;

                    if ($this->classification->isJunkQuestion(intval($questionId), $subject)) {
                        $output->writeln("Clearing junk data ".json_encode($jobData));
                        $this->queue->setQueueName(Queue::CLASSIFY)->deleteMessage($newJob);
                        continue;
                    }

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
                    $this->queue->setQueueName(Queue::CLASSIFY)->deleteMessage($newJob);
                    $output->writeln("Classification done for ".json_encode($jobData));
                } catch (\Exception $e) {
                    $output->writeln("Classification error for ".json_encode($jobData));
                    $output->writeln("Error ".$e->getMessage());
                    $output->writeln("Stacktrace ".$e->getTraceAsString());
                    throw $e;
                }
            }
        }
    }
}
