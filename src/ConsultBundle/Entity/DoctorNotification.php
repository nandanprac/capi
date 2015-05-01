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
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorNotificationRepository")
 * @ORM\Table(name="doctor_notifications")
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorNotification extends BaseEntity
{
    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     */
    protected $practoAccountId;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="doctorNotification")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $question;

    /**
     * @ORM\Column(name="notification_text", type="text")
     */
    protected $notificationText;

    /**
     * @ORM\Column(type="smallint", name="is_viewed")
     */
    protected $isViewed=0;
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @var integer
     */
    private $softDeleted;


    /**
     * Set practoAccountId
     *
     * @param integer $practoAccountId
     * @return DoctorNotification
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
     * Set notificationText
     *
     * @param string $notificationText
     * @return DoctorNotification
     */
    public function setNotificationText($notificationText)
    {
        $this->notificationText = $notificationText;

        return $this;
    }

    /**
     * Get notificationText
     *
     * @return string 
     */
    public function getNotificationText()
    {
        return $this->notificationText;
    }

    /**
     * Set isViewed
     *
     * @param integer $isViewed
     * @return DoctorNotification
     */
    public function setIsViewed($isViewed)
    {
        $this->isViewed = $isViewed;

        return $this;
    }

    /**
     * Get isViewed
     *
     * @return integer 
     */
    public function getIsViewed()
    {
        return $this->isViewed;
    }

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return DoctorNotification
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     * @return DoctorNotification
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime 
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set softDeleted
     *
     * @param integer $softDeleted
     * @return DoctorNotification
     */
    public function setSoftDeleted($softDeleted)
    {
        $this->softDeleted = $softDeleted;

        return $this;
    }

    /**
     * Get softDeleted
     *
     * @return integer 
     */
    public function getSoftDeleted()
    {
        return $this->softDeleted;
    }

    /**
     * Set question
     *
     * @param \ConsultBundle\Entity\Question $question
     * @return DoctorNotification
     */
    public function setQuestion(\ConsultBundle\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \ConsultBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }
}
