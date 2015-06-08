<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 05/06/15
 * Time: 13:01
 */

namespace ConsultBundle\Response;

/**
 * Class DetailQuestionResponseObject
 *
 * @package ConsultBundle\Response
 */
class DetailQuestionResponseObject extends BasicQuestionResponseObject
{

    /**
     * @var array $replies
     */
    private $replies;

    private $patientInfo;

    /**
     * @var int $bookmarkId
     */
    private $bookmarkId;

    /**
     * @var array
     */
    private $comments;

    /**
     * @var integer
     */
    private $commentsCount;
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



    /**
     * @return mixed
     */
    public function getPatientInfo()
    {
        return $this->patientInfo;
    }

    /**
     * @param mixed $patientInfo
     */
    public function setPatientInfo($patientInfo)
    {
        $this->patientInfo = $patientInfo;
    }

    /**
     * @return array
     */
    public function getReplies()
    {
        return $this->replies;
    }

    /**
     * @param array $replies
     */
    public function setReplies($replies)
    {
        $this->replies = $replies;
    }

    /**
     * @param \ConsultBundle\Response\ReplyResponseObject $reply
     */
    public function addReply(ReplyResponseObject $reply)
    {
        $this->replies[] = $reply;
    }

    /**
     * @return int
     */
    public function getBookmarkId()
    {
        return $this->bookmarkId;
    }

    /**
     * @param int $bookmarkId
     */
    public function setBookmarkId($bookmarkId)
    {
        $this->bookmarkId = $bookmarkId;
    }

    /**
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param array $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return int
     */
    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    /**
     * @param int $commentsCount
     */
    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;
    }
}