<?php

namespace ConsultBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use ConsultBundle\Constants\ConsultConstants;

/**
 * This command consits of actions to create stop words, add stem words to tags and adjust stem words
 */
class TrainingSetHelperCommand extends ContainerAwareCommand
{

    private $ADJUSTMENTWEIGHT = 100;
    private $COMPROMISINGWEIGHT = 200;

    private $container;
    private $classification;
    private $redis;
    private $wordManager;

    /**
     * Initialize Connections
     * @param InputInterface  $input input
     * @param OutputInterface $output output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->container = $this->getContainer();
        $this->classification =$this->container->get('consult.classification');
        $this->redis = $this->container->get('consult.redis');
        $this->wordManager = $this->container->get('consult.word_manager');
    }

    /*
     * Configure the task with options and arguments
     */
    protected function configure()
    {
        $this->setName('consult:data:trainer:helper')
            ->setDescription('Command to train the question classification map')
            ->addArgument('files', InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
            ->addOption('action', null, InputOption::VALUE_REQUIRED, "Action to be taken, options are stop, stem, adjust");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePaths = $input->getArgument('files');
        $action = $input->getOption('action');

        $data = $this->classification->readCSV($filePaths);
        if ($action == 'stop') {
            $words = array();
            foreach ($data as $each) {
                array_push($words, $each[0]);
            }
            $this->wordManager->addStopWords($words);
        } elseif ($action == 'stem') {
            foreach ($data as $each) {
                $synTag = $this->wordManager->loadByWord($each[1], ConsultConstants::SYN_TAG_ENTITY_NAME);
                if (!empty($synTag)) {
                    $this->wordManager->createSynTag($each[0], $synTag[0][1]);
                }
            }
        } elseif ($action == 'adjust') {
            foreach ($data as $map) {
                if ($this->redis->keyExists(strtolower($map[0]))) {
                    $scoreData = $this->redis->getKey(strtolower($map[0]));
                    try {
                        $tempScoreData = json_decode($scoreData, true);
                        if (!$tempScoreData) {
                            $map[0] = $scoreData;
                            $scoreData = $this->redis->getKey($map[0]);
                            $scoreData = json_decode($scoreData, true);
                        } else {
                            $scoreData = $tempScoreData;
                        }
                    } catch (\Exception $e) {
                        $output->writeln($map[0]);
                        $output->writeln($e->getMessage());
                        continue;
                    }
                    if (in_array($map[1], array_keys($scoreData))) {
                        $scoreData[$map[1]]['weight_score'] += $this->ADJUSTMENTWEIGHT;
                        $scoreData = $this->classification->formulaScoreUpdate($scoreData);
                    } else {
                        $scoreData[$map[1]]['weight_score'] = $this->COMPROMISINGWEIGHT;
                        $scoreData = $this->classification->formulaScoreUpdate($scoreData);
                    }
                    $this->redis->setKey($map[0], json_encode($scoreData));
                }
            }
        } else {
            $output->writeln('<error>Please pass one of these (stem, stop, adjust) for action option</error>');
        }

    }
}
