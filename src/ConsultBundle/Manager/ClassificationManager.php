<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Manager\RedisClient;

/**
 * Question Speciality Classification
 */
class ClassificationManager
{
    /**
     * Constructor
     *
     * @param RedisClient $redis - Redis Client
     *
     */
    public function __construct(RedisClient $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Takes in array of csv file paths and return list of rows
     *
     * @param array  $filePaths - path to csv files
     * @param string $delim     - Separation param used in csv
     *
     * @return array
     */
    public function readCSV(array $filePaths, $delim = '|')
    {
        $data = array();

        foreach ($filePaths as $filePath) {
            $file = fopen($filePath, 'r');
            while (($line = fgetcsv($file, 0, "|")) !== false) {
                array_push($data, $line);
            }
            fclose($file);
        }

        return $data;
    }

    /**
     * Takes in sentence and return a list of words
     *
     * @param string $sentence - sentence containing user question and tags
     *
     * @return array words
     */
    public function sentenceWords($sentence)
    {
        $sentence = preg_replace('/[^A-Za-z]/', ' ', $sentence);
        $words = array();
        $sentence = preg_split('/\s+/', strtolower($sentence));
        $stopWords = $this->redis->getKey('stop_words');
        if (!$stopWords) {
            $stopWords = array();
        }
        foreach ($sentence as $word) {
            if (!in_array($word, $words) and !in_array($words, $stopWords) and strlen($word) > 2)
            {
                //if (array_key_exists($word, $stemWords))
                //    array_push($words, $stemWords[$word]);
                //else
                array_push($words, $word);
            }
        }

        return $words;
    }

    /**
     * Takes in list of hash maps containing score hash map of each word
     * Intersecting these hash maps will give us likelyhood of falling all words in one category.
     *
     * @param array listDataMap - array of associative array of all words
     *
     * @return array of most likely specialities
     */
    protected function intersectMapsForWords($listDataMap)
    {
        $weightTemp = array();
        $formulaTemp = array();

        foreach ($listDataMap as $map) {
            foreach ($map as $speciality => $weights){
                if(!array_key_exists($speciality, $weightTemp)){
                    $weightTemp[$speciality] = $weights['weight_score'];
                } else {
                    $weightTemp[$speciality] += $weights['weight_score'];
                }

                if(!array_key_exists($speciality, $formulaTemp)){
                    $formulaTemp[$speciality] = $weights['formula_score'];
                } else {
                    $formulaTemp[$speciality] += $weights['formula_score'];
                }

                if ($weights['weight_score'] == 0)
                    unset($weightTemp[$speciality]);

                if ($weights['formula_score'] == 0)
                    unset($formulaTemp[$speciality]);
            }
        }
        arsort($weightTemp, SORT_NUMERIC);
        arsort($formulaTemp, SORT_NUMERIC);
        return array(array_slice($weightTemp, 0, 2), array_slice($formulaTemp, 0, 2));
    }

    /**
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
            if($this->redis->keyExists($word)){
                try{
                    $tempScoreData = $this->redis->getKey($word);
                    $scoreData = json_decode($tempScoreData, true);
                    if (!$scoreData) {
                        $scoreData = json_decode($this->redis->getKey($tempScoreData));
                    }
                } catch(\Exception $e) {
                    continue;
                }
                array_push($temp, $scoreData);
            }
        }
        return $this->getAppropriateSpeciality($this->intersectMapsForWords($temp));
    }

    /**
     * Takes intersection of specialities and returns the best suitable speciality
     *
     * @param array $intersectMap
     *
     * @return null/string
     */
    public function getAppropriateSpeciality(Array $intersectMap){
        if (count($intersectMap) == 2) {
            $a = array_keys($intersectMap[0]);
            $b = array_keys($intersectMap[1]);
            if ($a[0] == $b[0] or $a[1] == $b[0]) {
                return $b[0];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Takes in map and recalculates the formula score
     *
     * @param array $map
     *
     * $return array
     */
    public function formulaScoreUpdate($map){
        $termFreq = 0;
        foreach(array_values($map) as $speciality){
            $termFreq += $speciality['weight_score'];
        }
        foreach(array_keys($map) as $category){
            $map[$category]['formula_score'] = floatval($map[$category]['weight_score'])/floatval($termFreq)* floatval(1)/floatval(1+log10($termFreq));
        }
        return $map;
    }
}