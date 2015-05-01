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
     * Constructor
     */
    public function __construct()
    {
        $this->ratings = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set answerText
     *
     * @param string $answerText
     * @return DoctorReply
     */
    public function setAnswerText($answerText)
    {
        $this->answerText = $answerText;

        return $this;
    }

    /**
     * Get answerText
     *
     * @return string 
     */
    public function getAnswerText()
    {
        return $this->answerText;
    }

    /**
     * Set isSelected
     *
     * @param integer $isSelected
     * @return DoctorReply
     */
    public function setIsSelected($isSelected)
    {
        $this->isSelected = $isSelected;

        return $this;
    }

    /**
     * Get isSelected
     *
     * @return integer 
     */
    public function getIsSelected()
    {
        return $this->isSelected;
    }

    /**
     * Set viewedAt
     *
     * @param \DateTime $viewedAt
     * @return DoctorReply
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
     * @return DoctorReply
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
     * @return DoctorReply
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
     * @return DoctorReply
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
     * Set doctorQuestion
     *
     * @param \ConsultBundle\Entity\DoctorQuestion $doctorQuestion
     * @return DoctorReply
     */
    public function setDoctorQuestion(\ConsultBundle\Entity\DoctorQuestion $doctorQuestion = null)
    {
        $this->doctorQuestion = $doctorQuestion;

        return $this;
    }

    /**
     * Get doctorQuestion
     *
     * @return \ConsultBundle\Entity\DoctorQuestion 
     */
    public function getDoctorQuestion()
    {
        return $this->doctorQuestion;
    }

    /**
     * Add ratings
     *
     * @param \ConsultBundle\Entity\DoctorReplyRating $ratings
     * @return DoctorReply
     */
    public function addRating(\ConsultBundle\Entity\DoctorReplyRating $ratings)
    {
        $this->ratings[] = $ratings;

        return $this;
    }

    /**
     * Remove ratings
     *
     * @param \ConsultBundle\Entity\DoctorReplyRating $ratings
     */
    public function removeRating(\ConsultBundle\Entity\DoctorReplyRating $ratings)
    {
        $this->ratings->removeElement($ratings);
    }

    /**
     * Get ratings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRatings()
    {
        return $this->ratings;
    }
}
