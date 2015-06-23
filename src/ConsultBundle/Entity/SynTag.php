<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SynTag
 *
 * @ORM\Entity
 * @ORM\Table(name="syn_tag")
 * @ORM\HasLifecycleCallbacks()
 */
class SynTag extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="word", type="string", length=255)
     */
    private $word;

    /**
     * @ORM\ManyToOne(targetEntity = "WordScore")
     * @ORM\JoinColumn(name = "score_id", referencedColumnName = "id")
     */
    private $score;

    /**
     * Set word
     *
     * @param string $word
     * @return SynTag
     */
    public function setWord($word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * Set score
     *
     * @param WordScore $score
     * @return SynTag
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return string
     */
    public function getScore()
    {
        return $this->score;
    }
}
