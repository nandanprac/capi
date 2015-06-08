<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:49
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="doctor_replies")
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorReply extends BaseEntity
{
    /**
     * @ORM\OneToOne(targetEntity="DoctorQuestion", inversedBy = "doctorReply")
     * @ORM\JoinColumn(name="doctor_question_id", referencedColumnName="id")
    */
    protected $doctorQuestion;

    /**
     * @ORM\Column(type="text", name="text")
     */
    protected $text;

    /**
     * rating by the person who asked the question
     * @ORM\Column(type="smallint", name="rating", nullable=true)
     */
    protected $rating = null;

    /**
     * @ORM\Column(type="datetime", name="viewed_at", nullable=true)
     */
    protected $viewedAt;

    /**
     * @ORM\OneToMany(targetEntity="DoctorReplyVote", mappedBy="reply", cascade={"persist", "remove"})
     * @var ArrayCollection $likes
     */
    protected $votes;

    /**
     * Construct the object
     */
    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    /**
     * Set Doctor Question
     *
     * @param DoctorQuestion $doctorQuestion - Doctor Question
     */
    public function setDoctorQuestion($doctorQuestion)
    {
        $this->doctorQuestion = $doctorQuestion;
    }

    /**
     * Get Doctor Question
     *
     * @return DoctorQuestion
     */
    public function getDoctorQuestion()
    {
        return $this->doctorQuestion;
    }

    /**
     * Get DoctorQuestionId
     *
     * @return integer
     */
    public function getDoctorQuestionId()
    {
        if ($this->doctorQuestion) {
            return $this->getDoctorQuestion()->getId();
        }

        return null;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set Text
     *
     * @param string $text - Text
     */
    public function setText($text)
    {
        $this->setString('text', $text);
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
     * Get viewedAtStr
     *
     * @return string
     */
    public function getViewedAtStr()
    {
        return $this->getDateTimeStr($this->viewedAt);
    }

    /**
     * Set ViewedAt
     *
     * @param mixed $viewedAt - string or DateTime object
     */
    public function setViewedAt($viewedAt)
    {
        //$this->setDateTime('viewedAt', $viewedAt);
        $this->viewedAt = $viewedAt;
    }


    /**
     * @param \ConsultBundle\Entity\DoctorReplyVote $vote
     */
    public function addVote(DoctorReplyVote $vote)
    {
        if (!$vote->isSoftDeleted()) {
            $this->votes[] = $vote;
        }

    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return ArrayCollection
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param ArrayCollection $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }
}
