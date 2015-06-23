<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WordScore
 *
 * @ORM\Entity
 * @ORM\Table(name="word_score")
 * @ORM\HasLifecycleCallbacks()
 */
class WordScore extends BaseEntity
{
    /**
     * @var array
     *
     * @ORM\Column(name="score", type="json_array")
     */
    private $score;

    /**
     * Set score
     *
     * @param array $score
     * @return WordScore
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return array
     */
    public function getScore()
    {
        return $this->score;
    }
}
