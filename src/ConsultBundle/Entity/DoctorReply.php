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
    * @ORM\Column(type="integer", name="doctor_question_id")
    */
    protected $doctorQuestionId;

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
    protected $ratings;

    public function _construct()
    {
        $this->ratings = new ArrayCollection();
    }

    /**
     * @param $doctorQuestionId
     */
    public function setDoctorQuestionId($doctorQuestionId)
    {
        $this->doctorQuestionId = $doctorQuestionId;
    }

    /**
     * @return mixed
     */
    public function getDoctorQuestionId()
    {
        return $this->doctorQuestionId;
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
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * Add Ratings
     *
     * @param DoctorReplyRating $rating - Doctor Reply Rating
     */
    public function addRating(DoctorReplyRating $rating)
    {
        $this->ratings[] = $rating;
    }

    /**
     * Clear Ratings
     */
    public function clearRatings()
    {
        $this->ratings = new ArrayCollection();
    }
}
