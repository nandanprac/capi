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
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorQuestionRepository")
 * @ORM\Table(name="doctor_questions")
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorQuestion extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy ="doctorQuestion")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $question;

    /**
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    protected $practoAccountId;

    /**
     * @ORM\Column(length=10, name="state")
     */
    protected $state="UNANSWERED";

    /**
     * @ORM\Column(name="rejection_reason", length=100, nullable=true)
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
     * @ORM\OneToOne(targetEntity = "DoctorReply", mappedBy = "doctorQuestion")
     */
    protected $doctorReply;


    /**
     * Get PractoAccountId
     *
     * @return integer
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * Set PractoAccountId
     *
     * @param integer $practoAccountId - PractoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->setInt('practoAccountId', $practoAccountId);
    }

    /**
     * Set Question
     *
     * @param Question $question - Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * Get Question
     *
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Get QuestionId
     *
     * @return integer
     */
    public function getQuestionId()
    {
        if ($this->question) {
            return $this->question->getId();
        }

        return null;
    }

    /**
     * Get rejectedAt
     *
     * @return DateTime
     */
    public function getRejectedAt()
    {
        return $this->rejectedAt;
    }

    /**
     * Get rejectedAtStr
     *
     * @return string
     */
    public function getRejectedAtStr()
    {
        return $this->getDateTimeStr('rejectedAt');
    }

    /**
     * Set rejectedAt
     *
     * @param mixed $rejectedAt - string or DateTime object
     */
    public function setRejectedAt($rejectedAt)
    {
        $this->setDateTime('rejectedAt', $rejectedAt);
    }

    /**
     * Get viewedAt
     *
     * @return DateTime
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    /**
     * Get viewedAtStr
     *
     * @return string
     */
    public function getViewedAtStr()
    {
        return $this->getDateTimeStr('viewedAt');
    }

    /**
     * Set ViewedAt
     *
     * @param mixed $viewedAt - string or DateTime object
     */
    public function setViewedAt($viewedAt)
    {
        $this->setDateTime('viewedAt', $viewedAt);
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
     * Set State
     *
     * @param string $state - State
     */
    public function setState($state)
    {
        $this->setString('state', $state);
    }

    /**
     * Get Rejection Reason
     *
     * @return string
     */
    public function getRejectionReason()
    {
        return $this->rejectionReason;
    }

    /**
     * Set Rejection Reason
     *
     * @param string $rejectionReason - Rejection Reason
     */
    public function setRejectionReason($rejectionReason)
    {
        $this->setString('rejectionReason', $rejectionReason);
    }

    public function _construct()
    {
        $this->doctorReply = new ArrayCollection();
    }

    /**
     * Get Doctor Reply
     *
     * @return ArrayCollection
     */
    public function getDoctorReplies()
    {
        return $this->doctorReply;
    }

    /**
     * Add Doctor Reply
     *
     * @param DoctorReply $doctorReply - Doctor Reply
     */
    public function addDoctorReply(DoctorReply $doctorReply)
    {
        $this->doctorReply[] = $doctorReply;
    }

    /**
     * Clear Doctor Reply
     */
    public function clearDoctorReplies()
    {
        $this->doctorReply = new ArrayCollection();
    }
}
