<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 13:34
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorQuestionsRepository")
 * @ORM\Table(name="doctor_questions")
 */
class DoctorQuestion extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy ="doctorQuestion")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $question;

    /**
     * @ORM\OneToOne(targetEntity = "DoctorReply", mappedBy = "doctorQuestion")
     */
    protected $doctorReply;

    /**
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    protected $practoAccountId;

    /**
     * @ORM\Column(length=10)
     */
    protected $state;

    /**
     * @ORM\Column(name="rejection_reason", length=10, nullable=true)
     */
    protected $rejectionReason;

    /**
     * @ORM\Column(name="rejected_at", type="datetime", nullable=true)
     */
    protected $rejectedAt;

    /**
     * @ORM\Column(name="viewed_at", type="datetime", nullable=true)
     */
    protected $viewedAt;
}
