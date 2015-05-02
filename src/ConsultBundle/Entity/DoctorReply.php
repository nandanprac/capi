<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:49
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorReplyRepository")
 * @ORM\Table(name="doctor_replies")
 */
class DoctorReply extends BaseEntity
{
   /**
    * @ORM\OneToOne(targetEntity="DoctorQuestion", inversedBy = "doctorReply")
    */
    protected $doctorQuestion;

    /**
     * @ORM\Column(type="text", name="answer_text")
     */
    protected $answerText;

    /**
     * @ORM\Column(type="smallint", name="is_selected")
     */
    protected $isSelected = 0;

    /**
     * @ORM\Column(type="datetime", name="viewed_at", nullable=true)
     */
    protected $viewedAt;

    /**
     * @ORM\OneToMany(targetEntity="DoctorReplyRating", mappedBy="doctorReply")
     */
    protected $ratings;

    /**
     * @return mixed
     */
    public function getDoctorQuestion()
    {
        return $this->doctorQuestion;
    }

    /**
     * @param mixed $doctorQuestion
     */
    public function setDoctorQuestion($doctorQuestion)
    {
        $this->doctorQuestion = $doctorQuestion;
    }

    /**
     * @return mixed
     */
    public function getAnswerText()
    {
        return $this->answerText;
    }

    /**
     * @param mixed $answerText
     */
    public function setAnswerText($answerText)
    {
        $this->answerText = $answerText;
    }

    /**
     * @return mixed
     */
    public function getIsSelected()
    {
        return $this->isSelected;
    }

    /**
     * @param mixed $isSelected
     */
    public function setIsSelected($isSelected)
    {
        $this->isSelected = $isSelected;
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

    /**
     * @return mixed
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param mixed $ratings
     */
    public function setRatings($ratings)
    {
        $this->ratings = $ratings;
    }

}
