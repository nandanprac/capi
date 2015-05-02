<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:18
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\UserNotificationRepository")
 * @ORM\Table(name="patient_notifications")
 * @ORM\HasLifecycleCallbacks()
 */
class UserNotification extends BaseEntity
{
    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     */
    protected $practoAccountId;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="userNotifications")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $question;

    /**
     * @ORM\Column(name="notification_txt", type="text")
     */
    protected $notificationText;

    /**
     * @ORM\Column(type="smallint", name="is_viewed")
     */
    protected $isViewed;

    /**
     * @return mixed
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * @param mixed $practoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $practoAccountId;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getNotificationText()
    {
        return $this->notificationText;
    }

    /**
     * @param mixed $notificationText
     */
    public function setNotificationText($notificationText)
    {
        $this->notificationText = $notificationText;
    }

    /**
     * @return mixed
     */
    public function getIsViewed()
    {
        return $this->isViewed;
    }

    /**
     * @param mixed $isViewed
     */
    public function setIsViewed($isViewed)
    {
        $this->isViewed = $isViewed;
    }


}
