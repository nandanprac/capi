<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionCommentRepository")
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
     *  @ORM\Column(type="string", name="identifier")
     *
     *  @var string $identifier
     *
     *  @Assert\NotBlank
     */
    protected $identifier;

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
        $this->practoAccountId = $practoAccountId;
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
     * Set identifier
     *
     * @param  string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
