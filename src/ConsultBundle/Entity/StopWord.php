<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StopWord
 *
 * @ORM\Table(name="stop_words")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class StopWord extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="word", type="string", length=255)
     */
    private $word;

    /**
     * Set word
     *
     * @param string $word
     * @return StopWord
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
}
