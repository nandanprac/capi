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
 * NOT IN USE CURRENTLY
 *
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorReplyRatingRepository")
 * @ORM\Table(name="doctor_reply_ratings")
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorReplyRating extends BaseEntity
{
    /**
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    protected $practoAccountId;

    /**
     * @ORM\ManyToOne(targetEntity="DoctorReply", inversedBy="likes")
     * @ORM\JoinColumn(name="doctor_reply_id", referencedColumnName="id")
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
     * Get Doctor Reply
     *
     * @return DoctorReply
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
    public function setDoctorReply(DoctorReply $doctorReply)
    {
        $this->doctorReply = $doctorReply;
    }


}
