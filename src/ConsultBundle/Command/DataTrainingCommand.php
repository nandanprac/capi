<?php

namespace ConsultBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;


class DataTrainingCommand extends ContainerAwareCommand
{

    private $PRIORITY_WEIGHT = 100;
    private $NORMAL_WEIGHT = 1;

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
    }

    /*
     * Configure the task with options and arguments
     */
    protected function configure()
    {
        $this->setName('consult:data:trainer')
            ->setDescription('Command to train the question classification map')
            ->addArgument('files', InputArgument::IS_ARRAY | InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePaths = $input->getArgument('files');

        $data = $this->classification->readCSV($filePaths);
        $dataMap = array();
        $categoryMap = array();
        $highPriority = False;

        foreach ($data as $each) {
            $words = $this->classification->sentenceWords(implode(" ", array_slice($each, 0, -1)));
            if (count($words) == 1) {
                $highPriority = True;
            }

            foreach ($words as $word) {
                if(!in_array(current(array_slice($each, -1)), $categoryMap))
                    array_push($categoryMap, current(array_slice($each, -1)));

                if(!array_key_exists($word, $dataMap)){
                    $word = strtolower($word);
                    $dataMap[$word] = array();

                    if ($highPriority) {
                        $dataMap[$word][end($each)] = array();
                        $dataMap[$word][end($each)]['weight_score'] = $this->PRIORITY_WEIGHT;
                    } else {
                        $dataMap[$word][end($each)] = array();
                        $dataMap[$word][end($each)]['weight_score'] = $this->NORMAL_WEIGHT;
                    }
                } else {
                    if (!in_array(current(array_slice($each, -1)), $dataMap[$word])) {
                        if ($highPriority) {
                            $dataMap[$word][end($each)] = array();
                            $dataMap[$word][end($each)]['weight_score'] = $this->PRIORITY_WEIGHT;
                        } else {
                            $dataMap[$word][end($each)] = array();
                            $dataMap[$word][end($each)]['weight_score'] = $this->NORMAL_WEIGHT;
                        }
                        $dataMap[$word][end($each)]['weight_score'] += 1;
                    }
                }
            }
        }
        foreach ($dataMap as $word=>$map) {
            foreach ($categoryMap as $category) {
                if (in_array($category, array_keys($dataMap[$word]))) {
                    $termFreq = 0;
                    foreach(array_values($dataMap[$word]) as $speciality){
                        $termFreq += $speciality['weight_score'];
                    }
                    $dataMap[$word][$category]['formula_score'] = floatval($dataMap[$word][$category]['weight_score'])/floatval($termFreq)* floatval(1)/floatval(1+log10($termFreq));
                }
            }
            $this->redis->setKey($word, json_encode($dataMap[$word]));
        }

    }

}
