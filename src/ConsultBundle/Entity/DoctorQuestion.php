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
    public function getDoctorReply()
    {
        return $this->doctorReply;
    }

    /**
     * @param mixed $doctorReply
     */
    public function setDoctorReply($doctorReply)
    {
        $this->doctorReply = $doctorReply;
    }

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
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getRejectionReason()
    {
        return $this->rejectionReason;
    }

    /**
     * @param mixed $rejectionReason
     */
    public function setRejectionReason($rejectionReason)
    {
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * @return mixed
     */
    public function getRejectedAt()
    {
        return $this->rejectedAt;
    }

    /**
     * @param mixed $rejectedAt
     */
    public function setRejectedAt($rejectedAt)
    {
        $this->rejectedAt = $rejectedAt;
    }

    /**
     * @return mixed
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    /**
     * @param mixed $viewedAt
     */
    public function setViewedAt($viewedAt)
    {
        $this->viewedAt = $viewedAt;
    }


}
