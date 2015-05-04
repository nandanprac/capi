<?php

namespace ConsultBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use ConsultBundle\Queue\AbstractQueue as Queue;
use ConsultBundle\FabricDomain;

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
        $this->queue = $this->container->get('consult.queue');
    }
     /**
     * Configure the task.
     */
    protected function configure()
    {
        $this
            ->setName('consult:queue:indexing:test')
            ->setDescription('queue for indexing for search results.')
            ->addArgument('domain', InputArgument::OPTIONAL, 'Fabric Domain', 'http://www.practo.com');
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
        $this->container->set('consult.fabric_domain', $fabricDomain);
        $this->queue->setFabricDomain($fabricDomain);

        while (1) {
            $newJob = $this->queue
                ->setQueueName(Queue::PUSH_TEST)
                ->receiveMessage();
            if ($newJob) {
//                $output->writeln($newJob);
              $jobData = json_decode($newJob, true);
                $this->queue->setQueueName(Queue::PUSH_TEST)->deleteMessage($newJob);
            }
        }
    }
}
