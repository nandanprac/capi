<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 19/06/15
 * Time: 16:10
 */

namespace ConsultBundle\Response;


use ConsultBundle\Entity\QuestionComment;

class QuestionCommentResponse extends ConsultResponseObject {

    private $text;

    private $identifier;

    private $votes;

    private $vote=0;

    private $flag;

    private $flagText;

    /**
     * @param \ConsultBundle\Entity\QuestionComment $questionComment
     */
    public function __construct(QuestionComment $questionComment = null)
    {
        if (!empty($questionComment)) {
            parent::__construct($questionComment);
            $this->text = $questionComment->getText();
            $this->identifier = $questionComment->getIdentifier();
        }

    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return mixed
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param mixed $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $this->getInt($votes);
    }

    /**
     * @return int
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * @param int $vote
     */
    public function setVote($vote)
    {
        $this->vote = $this->getInt($vote);
    }/**
 * @return mixed
 */
    public function getFlag()
    {
        return $this->flag;
    }/**
 * @param mixed $flag
 */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }/**
 * @return mixed
 */
    public function getFlagText()
    {
        return $this->flagText;
    }/**
 * @param mixed $flagText
 */
    public function setFlagText($flagText)
    {
        $this->flagText = $flagText;
    }





}