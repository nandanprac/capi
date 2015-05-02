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
}
