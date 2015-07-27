<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 05/06/15
 * Time: 13:01
 */

namespace ConsultBundle\Response;

use ConsultBundle\Entity\Question;
use ConsultBundle\Entity\UserInfo;
use ConsultBundle\Utility\Utility;

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

    /**
     * @var BasicPatientInfoResponse
     */
    private $patientInfo;

    /**
     * @var bool $bookmarkId
     */
    private $isBookmarked=false;

    /**
     *  bool $isOwner
     */
    private $isOwner;

    /**
     * @var array
     */
    private $comments;

    /**
     * @var integer
     */
    private $commentsCount;

    /**
     * @param \ConsultBundle\Entity\Question $question
     * @param null                           $practoAccountId
     */
    public function __construct(Question $question, $practoAccountId = null)
    {
        parent::__construct($question);
        $this->populatePatientInfo($question->getUserInfo(), $practoAccountId);
    }

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
     * @return BasicPatientInfoResponse
     */
    public function getPatientInfo()
    {
        return $this->patientInfo;
    }

    /**
     * @param \ConsultBundle\Response\BasicPatientInfoResponse $patientInfo
     */
    public function setPatientInfo(BasicPatientInfoResponse $patientInfo)
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
     * @return boolean
     */
    public function isIsBookmarked()
    {
        return $this->isBookmarked;
    }

    /**
     * @param boolean $isBookmarked
     */
    public function setIsBookmarked($isBookmarked)
    {
        $this->isBookmarked = Utility::toBool($isBookmarked);
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
        $this->commentsCount = $this->getInt($commentsCount);
    }

    /**
     * @return boolean
     */
    public function getIsOwner()
    {
        return $this->isOwner;
    }

    /**
     * @param boolean $isOwner
     */
    public function setIsOwner($isOwner)
    {
        $this->isOwner = $isOwner;
    }


    protected function populatePatientInfo(UserInfo $userInfo, $practoAccountId = null)
    {
        $patientInfo = new BasicPatientInfoResponse();
        $patientInfo->setId($userInfo->getPractoAccountId());
        $patientInfo->setAge($userInfo->getAge());
        $patientInfo->setGender($userInfo->getGender());
        $patientInfo->setLocation($userInfo->getLocation());



        if ($userInfo->getPractoAccountId() == $practoAccountId) {
            $this->isOwner = true;
            $patientInfo->setId($userInfo->getId());
        }

        $this->patientInfo = $patientInfo;
    }
}
