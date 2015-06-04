<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Manager\RedisClient;

/*
 * Question Speciality Classification
 */
class ClassificationManager
{
	protected $stopWords;
	protected $stemWords;
	protected $dataMap;

	/*
	 * Constructor
	 *
	 * @param RedisClient    $redis    - Redis Client
	 *
	 */
	public function __construct(RedisClient $redis)
	{
		$this->stopWords = $redis->getKey("stop_words");
		$this->stemWords = $redis->getKey("stem_words");
		$this->dataMap = $redis->getKey("data_map");
	}

	/*
	 * Takes in array of csv file paths and return list of rows
	 *
	 * @param array $filePaths - path to csv files
	 */
	public function readCSV(array $filePaths, $delim = '|')
	{
		$data = array();

		foreach ($filePaths as $filePath) {
			$file = fopen($filePath, 'r');
			while (($line = fgetcsv($file)) !== FALSE) {
				array_push($data, $line);
			}
			fclose($file);
		}

		return $data;
	}

	/*
	 * Takes in sentence and return a list of words
	 *
	 * @param string $sentence - sentence containing user question and tags
	 *
	 * @return array words
	 */
	protected function sentenceWords($sentence)
	{
		$sentence = preg_replace('/[^A-Za-z]/', ' ', $sentence);
		$words = array();
		$sentence = preg_split('/\s+/', strtolower($sentence));
		foreach ($sentence as $word) {
			if (!in_array($word, $words) and !in_array($words, $this->stopWords) and strlen($word) > 2)
			{
				if (array_key_exists($word, $this->stemWords))
					array_push($words, $this->stemWords[$word]);
				else
					array_push($word);
			}
		}
	}

	/*
	 * Takes in list of hash maps containing score hash map of each word
	 * Intersecting these hash maps will give us likelyhood of falling all words in one category.
	 *
	 * @param array listDataMap - array of associative array of all words
	 *
	 * @return array of most likely specialities
	 */
	protected function intersectMapsForWords($listDataMap)
	{
		$weight_temp = array();
		$formula_temp = array();

		foreach ($listDataMap as $map) {
			foreach ($map as $speciality=>$weights){
				if(!array_key_exists($weight_temp, $speciality)){
					$weight_temp[$speciality] = $weights['weight_score'];
				} else {
					$weight_temp[$speciality] += $weights['weight_score'];
				}

				if(!array_key_exists($formula_temp, $speciality)){
					$formula_temp[$speciality] = $weights['formula_score'];
				} else {
					$formula_temp[$speciality] += $weights['formula_score'];
				}

				if ($weights['weight_score'] == 0)
					unset($weight_temp[$speciality]);

				if ($weights['formula_score'] == 0)
					unset($formula_temp[$speciality]);
			}
		}
		return array(array_slice(arsort($weight_temp, SORT_NUMERIC), 0, 2), array_slice(arsort($formula_temp, SORT_NUMERIC), 0, 2));
	}

	/*
	 * Classification function.
	 *
	 * @param string sentence
	 *
	 * @return array classifcation
	 */
	public function classifi($sentence)
	{
		$words = $this->sentenceWords($sentence);

		$temp = array();
		foreach ($words as $word){
			if(in_array($word, $this->dataMap)){
				array_push($temp, $word);
			}
		}
		return 	$this->intersectMapsForWords($temp);
	}
}
