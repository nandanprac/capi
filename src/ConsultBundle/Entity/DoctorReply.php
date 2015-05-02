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
}
