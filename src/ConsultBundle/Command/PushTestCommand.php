<?php

namespace ConsultBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpFoundation\Request;
use ConsultBundle\Queue\AbstractQueue as Queue;
use ConsultBundle\FabricDomain;

/**
 * Command to merge the accounts and Make the necessary updates.
 */
class PushTestCommand extends ContainerAwareCommand
{
    protected $queue;

    /**
     * Initialize Queue
     * @param InputInterface  $input  input
     * @param OutputInterface $output output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->container = $this->getContainer();
        $this->queue = $this->container->get('consult.queue');

    }

    /**
     * Configure the task with options and arguments
     */
    protected function configure()
    {
        $this->setName('consult:queue:indexing:push')
            ->setDescription('Perform the merge account updates.')
            ->addArgument('domain', InputArgument::OPTIONAL, 'Fabric Domain', 'https://www.practo.com');
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
        $fabricDomain = new FabricDomain($request);
        //var_dump($fabricDomain);die;
        $this->container->set('consult.fabric_domain', $fabricDomain);
        $this->queue->setFabricDomain($fabricDomain);
        //for($i=0;$i<100;$i++){
        $this->queue->setQueueName(Queue::PUSH_TEST);
        var_dump($this->queue->sendMessage(5));die;
        //}
    }
}
