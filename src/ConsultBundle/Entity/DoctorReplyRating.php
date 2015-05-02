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
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorReplyRatingRepository")
 * @ORM\Table(name="doctor_reply_ratings")
 */
class DoctorReplyRating extends BaseEntity
{
    /**
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    protected $practoAccountId;

    /**
     * @ORM\ManyToOne(targetEntity="DoctorReply", inversedBy="ratings")
     * @ORM\JoinColumn(name="doctor_reply_id", referencedColumnName="id")
     */
    protected $doctorReply;

    /**
     * @ORM\Column(type="smallint", name="rating")
     */
    protected $rating;

    public function _construct()
    {
        $this->doctorReply = new ArrayCollection();
    }

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
     * Get Rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set Rating
     *
     * @param integer $rating - Rating
     */
    public function setRating($rating)
    {
        $this->setInt('rating', $rating);
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
