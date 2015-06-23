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
    protected $questionManager;
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
        $this->helper = $this->container->get('consult.helper');
    }
     /**
     * Configure the task.
     */
    protected function configure()
    {
        $this
            ->setName('consult:question:assignmentpersistence:queue')
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
                $this->doctorQuestionManager = $this->container->get('consult.doctorQuestionManager');
                $this->questionManager = $this->container->get('consult.question_manager');
                try {
                    if ($jobData['state'] == 'UNCLASSIFIED' or $jobData['state'] == 'MISMATCH') {
                        $this->questionManager->setState($jobData['question_id'], $jobData['state']);
                    } elseif ($jobData['state'] == 'ASSIGNED') {
                        $this->doctorQuestionManager->setDoctorsForAQuestions($jobData['question_id'], $jobData['doctors']);
                        $this->questionManager->setState($jobData['question_id'], $jobData['state']);
<<<<<<< HEAD
                        $this->questionManager->symfonysetTagsByQuestionId($jobData['question_id'], array_merge(array($jobData['speciality']), $jobData['tags']));
||||||| merged common ancestors
                        $this->questionManager->setTagsByQuestionId($jobData['question_id'], array_merge(array($jobData['speciality']), $jobData['tags']));
=======
                        $this->questionManager->setTagsByQuestionId($jobData['question_id'], array_merge(array($jobData['speciality']), $jobData['tags']));
                        if ($jobData['user_classified'] == 0){
                            $this->questionManager->setSpeciality($jobData['question_id'], $jobData['speciality']);
                        }
>>>>>>> 4b9180e668ed20fb404b121303138560c12a3286
                        $jobData['type'] = 'new_question';
                        $jobData['user_ids'] = $jobData['doctors'];
                        $jobData['message'] = $jobData['question_id'];
                        unset($jobData['doctors']);
                        unset($jobData['state']);
                        unset($jobData['speciality']);
                        $this->queue
                            ->setQueueName(Queue::CONSULT_GCM)
                            ->sendMessage(json_encode($jobData));
                    } elseif ($jobData['state'] == 'GENERIC'  or $jobData['state'] == 'DOCNOTFOUND') {
<<<<<<< HEAD
                        $this->questionManager->setState($jobData['question_id'], $jobData['state']);
                        $this->questionManager->setTagsByQuestionId($jobData['question_id'], array_merge(array($jobData['speciality']), $jobData['tags']));
||||||| merged common ancestors
                        $this->questionManager->setState($jobData['question_id'], $jobData['state']);
                        $this->questionManager->setTagByQuestionId($jobData['question_id'], array_merge(array($jobData['speciality']), $jobData['tags']));
=======
                        if ($jobData['user_classified'] == 0){
                            $this->questionManager->setSpeciality($jobData['question_id'], $jobData['speciality']);
                        }
                           $this->questionManager->setState($jobData['question_id'], $jobData['state']);
                        $this->questionManager->setTagsByQuestionId($jobData['question_id'], array_merge(array($jobData['speciality']), $jobData['tags']));
>>>>>>> 4b9180e668ed20fb404b121303138560c12a3286
                    }
                    $output->writeln("Queue Message Persisted: ".json_encode($jobData));
                } catch (\Exception $e) {
                    $output->writeln("Dropping the queue message: ".json_encode($jobData));
                    $this->queue->setQueueName(Queue::ASSIGNMENT_UPDATE)->deleteMessage($newJob);
                    $output->writeln($e->getMessage());
                }
                $this->queue->setQueueName(Queue::ASSIGNMENT_UPDATE)->deleteMessage($newJob);
            }
        }
    }
}
