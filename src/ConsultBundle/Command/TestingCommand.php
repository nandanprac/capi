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
class TestingCommand extends ContainerAwareCommand
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
        $this->queue = $this->container->get("consult.consult_queue");
    }
     /**
     * Configure the task.
     */
    protected function configure()
    {
        $this
            ->setName('consult:queue:indexing:test')
            ->setDescription('queue for indexing for search results.')
            ->addArgument('question', InputArgument::OPTIONAL)
            ->addArgument('speciality', InputArgument::OPTIONAL)
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
        $question = $input->getArgument('question');
        $speciality = $input->getArgument('speciality');
        $consultDomain = new ConsultDomain($request);
        $this->container->set('consult.consult_domain', $consultDomain);
        $this->queue->setConsultDomain($consultDomain);
        $this->queue
            ->setQueueName(Queue::CLASSIFY)
            ->sendMessage(json_encode(array('question'=>$question ? $question : '', 'question_id'=>1, 'speciality'=>$speciality ? $speciality : '')));
    }
}
