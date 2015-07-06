<?php

namespace ConsultBundle\Response;

use ConsultBundle\Entity\UserNotification;

/**
 * Class UserNotificationResponseObject
 *
 * @package ConsultBundle\Response
 */
class UserNotificationResponseObject extends ConsultResponseObject
{
    /**
     * @var int
     */
    private $questionId;

    /**
     * @var int
     */
    private $practoAccountId;

    /**
     * @var string
     */
    private $text;

    /**
     * @var bool
     */
    private $viewed;

    /**
     * @param \ConsultBundle\Entity\UserNotification $userNotificationEntity
     */
    public function __construct(UserNotification $userNotificationEntity = null)
    {
        parent::__construct($userNotificationEntity);

        if (!is_null($userNotificationEntity)) {
            $this->settext($userNotificationEntity->gettext());
            $this->setViewed($userNotificationEntity->isViewed());
            $this->setquestionId($userNotificationEntity->getQuestion()->getId());
            $this->setpractoAccountId($userNotificationEntity->getpractoAccountId());
        }

    }

    /**
     * @return string
     */
    public function getquestionId()
    {
        return $this->questionId;
    }

    /**
     * @param string $questionId
     */
    public function setquestionId($questionId)
    {
        $this->questionId = $this->getInt($questionId);
    }

    /**
     * @return string
     */
    public function getviewed()
    {
        return $this->viewed;
    }

    /**
     * @param string $viewed
     */
    public function setviewed($viewed)
    {
        $this->viewed = $viewed;
    }

    /**
     * @return mixed
     */
    public function getpractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * @param mixed $practoAccountId
     */
    public function setpractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $this->getInt($practoAccountId);
    }

    /**
     * @return mixed
     */
    public function gettext()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function settext($text)
    {
        $this->text = $text;
    }

}
