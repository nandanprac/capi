<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="question_comments")
 * @ORM\HasLifecycleCallbacks()
 */

class QuestionComment extends BaseEntity
{
    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     *
     * @var integer $practoAccountId
     *
     * @Assert\NotBlank
     */
    protected $practoAccountId;

    /**
     * @ORM\Column(type="text", name="text")
     *
     * @Assert\NotBlank
     */
    protected $text;

    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy = "comments")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $question;


    /**
     * Set practoAccountId
     *
     * @param integer $practoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->setInt('practoAccountId', $practoAccountId);
    }

    /**
     * Get practoAccountId
     *
     * @return integer 
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return QuestionComment
     */
    public function setText($text)
    {
        $this->setString('text', $text);
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set question
     *
     * @param Question $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * Get question
     *
     * @return Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }
}