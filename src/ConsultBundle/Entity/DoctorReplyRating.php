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
     * @ORM\Column(name="doctor_reply_id", type="integer")
     */
    protected $doctorReplyId;

    /**
     * @return mixed
     */
    public function getDoctorReplyId()
    {
        return $this->doctorReplyId;
    }

    /**
     * @param mixed $doctorReplyId
     */
    public function setDoctorReplyId($doctorReplyId)
    {
        $this->doctorReplyId = $doctorReplyId;
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


}
