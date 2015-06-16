<?php

namespace ConsultBundle\Response;

use ConsultBundle\Entity\DoctorNotification;

/**
 * Class DoctorNotificationResponseObject
 *
 * @package ConsultBundle\Response
 */
class DoctorNotificationResponseObject extends ConsultResponseObject
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
     * @param \ConsultBundle\Entity\DoctorNotification $doctorNotificationEntity
     */
    public function __construct(DoctorNotification $doctorNotificationEntity = null)
    {
        parent::__construct($doctorNotificationEntity);

        if (!is_null($doctorNotificationEntity)) {
            $this->settext($doctorNotificationEntity->gettext());
            $this->setViewed($doctorNotificationEntity->isViewed());
            $this->setquestionId($doctorNotificationEntity->getQuestion()->getId());
            $this->setpractoAccountId($doctorNotificationEntity->getpractoAccountId());
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
        $this->questionId = $questionId;
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
        $this->practoAccountId = $practoAccountId;
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
