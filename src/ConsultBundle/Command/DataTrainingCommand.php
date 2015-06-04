<?php

namespace ConsultBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;


class DataTrainingCommand extends ContainerAwareCommand
{

	/**
	 * Initialize Connections
	 *
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

		$this->redis->setKey("test_key", array('1'=>'a', '2'=>'b'));
		die;
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
					array_push(current(array_slice($each, -1)),$categoryMap);

				if(!array_key_exists($word, $dataMap)){
					$word = strtolower($word);
					$dataMap[$word] = array();

					if ($highPriority) {
						$dataMap[$word][end($each)] = array();
						$dataMap[$word][end($each)]['weight_score'] = 100;
					} else {
						$dataMap[$word][end($each)] = array();
						$dataMap[$word][end($each)]['weight_score'] = 1;
					}
				} else {
					
				}
			}
		}
	}

}
