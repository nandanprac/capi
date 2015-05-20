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
 * @ORM\Entity
 * @ORM\Table(name="doctor_replies")
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorReply extends BaseEntity
{
   /**
    * @ORM\OneToOne(targetEntity="DoctorQuestion", inversedBy = "doctorReply")
    */
    protected $doctorQuestion;

    /**
     * @ORM\Column(type="text", name="text")
     */
    protected $text;

    /**
     * @ORM\Column(type="smallint", name="selected")
     */
    protected $selected = 0;

    /**
     * @ORM\Column(type="datetime", name="viewed_at", nullable=true)
     */
    protected $viewedAt;

    /**
     * @ORM\OneToMany(targetEntity="DoctorReplyRating", mappedBy="doctorReply")
     */
    protected $likes;

    public function _construct()
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
            return $this->doctorQuestion->getId();
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
     * Is Selected
     *
     * @return boolean
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * Set selected
     *
     * @param boolean $selected - Selected
     */
    public function setSelected($selected)
    {
        $this->setBoolean('selected', $selected);
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
     * Get ratings
     *
     * @return ArrayCollection
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Add Ratings
     *
     * @param DoctorReplyRating $rating - Doctor Reply Rating
     */
    public function addRating(DoctorReplyRating $like)
    {
        $this->likes[] = $like;
    }

    /**
     * Clear Ratings
     */
    public function clearRatings()
    {
        $this->likes = new ArrayCollection();
    }
}
