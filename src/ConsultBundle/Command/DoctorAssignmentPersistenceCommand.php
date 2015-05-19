<?php

namespace ConsultBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use ConsultBundle\Queue\AbstractQueue as Queue;
use ConsultBundle\ConsultDomain;

use Symfony\Component\HttpFoundation\Request;
/**
 * Command to queue index
 */
class DoctorAssignmentPersistenceCommand extends ContainerAwareCommand
{
    /**
     * Initialize Services
     *
     * @param InputInterface  $input  - Input Interface
     * @param OutputInterface $output - Output Interface
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->container = $this->getContainer();
        $this->queue = $this->container->get('consult.consult_queue');
        $this->doctorQuestionManager = $this->container->get('consult.doctorQuestionManager');
        $this->questionManager = $this->container->get('consult.question_manager');
    }
     /**
     * Configure the task.
     */
    protected function configure()
    {
        $this
            ->setName('consult:assignmentpersist:doctorassignment:queue')
            ->setDescription('queue for indexing for search results.')
            ->addArgument('domain', InputArgument::OPTIONAL, 'Fabric Domain', 'http://consult.practo.com');
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
                ->setQueueName(Queue::ASSIGNMENT_UPDATE)
                ->receiveMessage();
            if ($newJob) {
                $jobData = json_decode($newJob, true);
                $this->queue->setQueueName(Queue::ASSIGNMENT_UPDATE)->deleteMessage($newJob);
                try {
                  if ($jobData['state'] == 'UNCLASSIFIED' or $jobData['state'] == 'MISMATCH') {
                      $this->questionManager->setState($jobData['question_id'], $jobData['state']);
                  } elseif($jobData['state'] == 'ASSIGNED') {
                      $this->doctorQuestionManager->setDoctorsForAQuestions($jobData['question_id'], $jobData['doctors']);
                      $this->questionManager->setState($jobData['question_id'], $jobData['state']);
                  } elseif ($jobData['state'] == 'GENERIC') {
                      $this->questionManager->setState($jobData['question_id'], $jobData['state']);
                  }
                } catch (\Exception $e) {
                    $output->writeln($e->getMessage());
                }
            }
        }
    }
}
