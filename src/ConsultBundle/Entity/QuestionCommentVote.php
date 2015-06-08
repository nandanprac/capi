<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionCommentVote
 *
 * @ORM\Table(name="questions_comments_votes")
 * @ORM\Entity
 */
class QuestionCommentVote extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Column(name="question_comment_id", type="integer")
     */
    private $questionCommentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="vote", type="smallint")
     */
    private $vote;

    /**
     * @var integer
     *
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    private $practoAccountId;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set vote
     *
     * @param integer $vote
     * @return QuestionCommentVote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return integer
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set practoAccountId
     *
     * @param integer $practoAccountId
     * @return QuestionCommentVote
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $practoAccountId;

        return $this;
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
     * @return mixed
     */
    public function getQuestionCommentId()
    {
        return $this->questionCommentId;
    }

    /**
     * @param mixed $questionCommentId
     */
    public function setQuestionCommentId($questionCommentId)
    {
        $this->questionCommentId = $questionCommentId;
    }


}
