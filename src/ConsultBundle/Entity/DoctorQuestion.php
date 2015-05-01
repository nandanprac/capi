<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 13:34
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorQuestionsRepository")
 * @ORM\Table(name="doctor_questions")
 */
class DoctorQuestion extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy ="doctorQuestion")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $question;

    /**
     * @ORM\OneToOne(targetEntity = "DoctorReply", mappedBy = "doctorQuestion")
     */
    protected $doctorReply;

    /**
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    protected $practoAccountId;

    /**
     * @ORM\Column(length=10)
     */
    protected $state;

    /**
     * @ORM\Column(name="rejection_reason", length=10, nullable=true)
     */
    protected $rejectionReason;

    /**
     * @ORM\Column(name="rejected_at", type="datetime", nullable=true)
     */
    protected $rejectedAt;

    /**
     * @ORM\Column(name="viewed_at", type="datetime", nullable=true)
     */
    protected $viewedAt;
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
     * @return DoctorQuestion
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
     * Set state
     *
     * @param string $state
     * @return DoctorQuestion
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set rejectionReason
     *
     * @param string $rejectionReason
     * @return DoctorQuestion
     */
    public function setRejectionReason($rejectionReason)
    {
        $this->rejectionReason = $rejectionReason;

        return $this;
    }

    /**
     * Get rejectionReason
     *
     * @return string 
     */
    public function getRejectionReason()
    {
        return $this->rejectionReason;
    }

    /**
     * Set rejectedAt
     *
     * @param \DateTime $rejectedAt
     * @return DoctorQuestion
     */
    public function setRejectedAt($rejectedAt)
    {
        $this->rejectedAt = $rejectedAt;

        return $this;
    }

    /**
     * Get rejectedAt
     *
     * @return \DateTime 
     */
    public function getRejectedAt()
    {
        return $this->rejectedAt;
    }

    /**
     * Set viewedAt
     *
     * @param \DateTime $viewedAt
     * @return DoctorQuestion
     */
    public function setViewedAt($viewedAt)
    {
        $this->viewedAt = $viewedAt;

        return $this;
    }

    /**
     * Get viewedAt
     *
     * @return \DateTime 
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
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
     * @return DoctorQuestion
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
     * @return DoctorQuestion
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
     * @return DoctorQuestion
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
     * @return DoctorQuestion
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

    /**
     * Set doctorReply
     *
     * @param \ConsultBundle\Entity\DoctorReply $doctorReply
     * @return DoctorQuestion
     */
    public function setDoctorReply(\ConsultBundle\Entity\DoctorReply $doctorReply = null)
    {
        $this->doctorReply = $doctorReply;

        return $this;
    }

    /**
     * Get doctorReply
     *
     * @return \ConsultBundle\Entity\DoctorReply 
     */
    public function getDoctorReply()
    {
        return $this->doctorReply;
    }
}
