<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * QuestionCommentVote
 *
 * @ORM\Table(name="questions_comments_votes")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionCommentVote extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="ConsultBundle\Entity\QuestionComment")
     * @ORM\JoinColumn(name="question_comment_id", referencedColumnName="id")
     */
    private $questionComment;

    /**
     * @var integer
     *
     * @ORM\Column(name="vote", type="smallint")
     *
     * @Assert\Choice(choices = {"1","-1"}, message="Valid value for upvote/downvote is 1/-1 ")
     * @Assert\NotBlank
     */
    private $vote;

    /**
     * @var integer
     *
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    private $practoAccountId;


    /**
     * Set vote
     *
     * @param integer $vote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;
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
     * @return QuestionComment
     */
    public function getQuestionComment()
    {
        return $this->questionComment;
    }

    /**
     * @param QuestionComment $questionComment
     */
    public function setQuestionComment($questionComment)
    {
        $this->questionComment = $questionComment;
    }
}
