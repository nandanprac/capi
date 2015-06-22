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
            ->addArgument('tags', InputArgument::OPTIONAL)
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
        $tags = $input->getArgument('tags');
        $consultDomain = new ConsultDomain($request);
        $this->container->set('consult.consult_domain', $consultDomain);
        $this->queue->setConsultDomain($consultDomain);
        var_dump($this->queue->getQueueName());
        $this->queue
<<<<<<< HEAD
              ->setQueueName(Queue::DAA)
              ->sendMessage(json_encode(array('question'=>$question ? $question : '', 'tags'=>$tags?$tags:'')));
=======
              ->setQueueName(Queue::CONSULT_GCM)
              ->sendMessage(json_encode(array('user_id'=>2645, 'message'=>'I smoke alot. Is there any chance to lung cancer?')));
>>>>>>> master
    }
}
