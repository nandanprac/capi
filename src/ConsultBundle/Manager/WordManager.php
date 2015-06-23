<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Entity\SynTag;
use ConsultBundle\Entity\WordScore;
use ConsultBundle\Entity\StopWord;
use ConsultBundle\Constants\ConsultConstants;

/**
 * Word Fetch
 */
class WordManager extends BaseManager
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {

    }

    /**
     * Takes in Array of words and score map and add it to db
     *
     * @param array $word    - Word
     * @param array $scoreId - Id of the score record
     *
     * @return null
     */
    public function createSynTag($word, $scoreId)
    {
        $synTag = new SynTag();
        $synTag->setWord($word);
        $score = $this->helper->loadById($scoreId, ConsultConstants::WORD_SCORE_ENTITY_NAME);
        $synTag->setScore($score);
        $synTag->setCreatedAt();
        $this->helper->persist($synTag, 'true');
    }


    /**
     * Takes in Array of words and score map and add it to db
     *
     * @param array $wordMap - Map of Words and score
     *
     * @return null
     */
    public function addWordScore(array $wordMap)
    {
        $batchSize = 10;
        $count = 1;
        foreach ($wordMap as $word => $map) {
            $synTag = new SynTag();
            $synTag->setWord($word);

            $score = new WordScore();
            $score->setScore($map);
            $score->setCreatedAt();
            $this->helper->persist($score);

            $synTag->setScore($score);
            $synTag->setCreatedAt();
            $this->helper->persist($synTag);
            $count += 1;
            if (($count % $batchSize) === 0) {
                $this->helper->flush();
                $this->helper->clear();
            }
        }
        $this->helper->flush();
        $this->helper->clear();
    }

    /**
     * Takes in Array of words and add it to stop words table
     *
     * @param array $stopWords
     *
     * @return null
     */
    public function addStopWords(array $stopWords)
    {
        $batchSize = 10;
        $count = 1;
        foreach ($stopWords as $word) {
            $stopWord = new StopWord();
            $stopWord->setWord($word);

            $stopWord->setCreatedAt();
            $this->helper->persist($stopWord);
            $count += 1;
            if (($count % $batchSize) === 0) {
                $this->helper->flush();
                $this->helper->clear();
            }
        }
        $this->helper->flush();
        $this->helper->clear();
    }


    /**
     * Takes in word and return words which exist in table
     *
     * @param array  $word   - Word to be looked up
     * @param string $entity - Entity to loop in
     *
     * @return array
     */
    public function lookupWord($word, $entity)
    {
        $result = $this->helper->getRepository($entity)->findBy(array("word" => $word));
        $words = array();
        if ($result == null) {
            return array();
        }
        foreach ($result as $word) {
            array_push($words, $word->getWord());
        }

        return $words;
    }

    /**
     * Takes in word and return array of attributes for word
     *
     * @param array  $word   - Word to be looked up
     * @param string $entity - Entity to loop in
     *
     * @return array
     */
    public function loadByWord($word, $entity)
    {
        $result = $this->helper->getRepository($entity)->findBy(array("word" => $word));
        $words = array();
        if ($result == null) {
            return array();
        }
        foreach ($result as $word) {
            array_push($words, array($word->getWord(), $word->getScore()->getId(), $word->getScore()->getScore()));
        }

        return $words;
    }

    /**
     * Takes in score id and new Score Data
     *
     * @param integer $scoreId   - ScoreId
     * @param array   $scoreData - Score Data
     */
    public function updateScore($scoreId, $scoreData)
    {

        $score = $this->helper->loadById($scoreId, ConsultConstants::WORD_SCORE_ENTITY_NAME);
        $score->setScore($scoreData);
        $this->helper->persist($score, 'true');
    }

    /**
     * Takes in list of words and gives a array of scores
     *
     * @param array $words - array of words
     *
     * @return array scores
     */
    public function fetchScores($words)
    {
        $words = $this->loadByWord($words, ConsultConstants::SYN_TAG_ENTITY_NAME);
        $temp = array();
        foreach ($words as $word) {
            array_push($temp, $word[2]);
        }

        return $temp;
    }
}
