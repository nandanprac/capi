<?php

namespace ConsultBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use ConsultBundle\Constants\ConsultConstants;

/**
 * Data Training Command
 */
class DataTrainingCommand extends ContainerAwareCommand
{

    private $PRIORITYWEIGHT = 100;
    private $NORMALWEIGHT = 1;
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
        $this->setName('consult:data:trainer')
            ->setDescription('Command to train the question classification map')
            ->addArgument('files', InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
            ->addOption('stem', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePaths = $input->getArgument('files');

        if ($input->getOption('stem')){
            $stemFile = end($filePaths);
            $filePaths = array_slice($filePaths, 0, -1);
            $stemData = $this->classification->readCSV(array($stemFile));
            $stems = array();
            foreach ($stemData as $each) {
                $stems[$each[0]] = $each[1];
            }
        }
        //at the end use. code snippet from Training Set Helper to add stems to syntag.
        $data = $this->classification->readCSV($filePaths);
        $dataMap = array();
        $categoryMap = array();
        $highPriority = false;
        $i=0;
        foreach ($data as $each) {
            $words = $this->classification->sentenceWords(implode(" ", array_slice($each, 0, -1)));
            /*
             *     if (count($words) == 1) {
             *       $highPriority = true;
             *     }
             */

            foreach ($words as $word) {
                if (!in_array(current(array_slice($each, -1)), $categoryMap)) {
                    array_push($categoryMap, current(array_slice($each, -1)));
                }

                $word = strtolower($word);
                if (!array_key_exists($word, $dataMap)) {
                    $dataMap[$word] = array();

                    if ($highPriority) {
                        $dataMap[$word][end($each)] = array();
                        $dataMap[$word][end($each)]['weight_score'] = $this->PRIORITYWEIGHT;
                    } else {
                        $dataMap[$word][end($each)] = array();
                        $dataMap[$word][end($each)]['weight_score'] = $this->NORMALWEIGHT;
                    }
                } else {
                    if (!in_array(current(array_slice($each, -1)), array_keys($dataMap[$word]))) {
                        if ($highPriority) {
                            $dataMap[$word][end($each)] = array();
                            $dataMap[$word][end($each)]['weight_score'] = $this->PRIORITYWEIGHT;
                        } else {
                            $dataMap[$word][end($each)] = array();
                            $dataMap[$word][end($each)]['weight_score'] = $this->NORMALWEIGHT;
                        }
                    }
                    $dataMap[$word][end($each)]['weight_score'] += 1;
                }
            }
        }
        foreach ($dataMap as $word => $map) {
            foreach ($categoryMap as $category) {
                if (in_array($category, array_keys($dataMap[$word]))) {
                    $termFreq = 0;
                    foreach (array_values($dataMap[$word]) as $speciality) {
                        $termFreq += $speciality['weight_score'];
                    }
                    $dataMap[$word][$category]['formula_score'] = floatval($dataMap[$word][$category]['weight_score'])/floatval($termFreq)* floatval(1)/floatval(1+log10($termFreq));
                }
            }
        }
        $this->wordManager->addWordScore($dataMap);

        $batchSize = 20;
        $count = 0;
        $dataToPush = array();
        foreach ($stemData as $each) {
            $synTag = $this->wordManager->loadByWord($each[1], ConsultConstants::SYN_TAG_ENTITY_NAME);
            if (!empty($synTag)) {
                array_push($dataToPush, array($each[0], $synTag[0][1]));
                if (($count % $batchSize) == 0) {
                    $this->wordManager->createSynTag($dataToPush);
                    $dataToPush = array();
                }
                $count += 1;
            }
        }
        $this->wordManager->createSynTag($dataToPush);
    }
}
