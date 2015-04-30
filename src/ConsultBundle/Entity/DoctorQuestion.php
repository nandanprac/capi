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
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy ="doctorQuestions")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $question;

    /**
     * @ORM\OnetoOne(targetEntity = "DoctorReply", mappedBy = "doctorQuestion")
     */
    protected $doctorReply;

    /**
     * @ORM\Column(type="integer")
     */
    protected $practo_account_id;

    /**
     * @ORM\Column(length=10)
     */
    protected $state;

    /**
     * @ORM\Column(length=10, nullable=true)
     */
    protected $rejection_reason;

    /**
     * @ORM\Column(name="rejection_at", type="datetime", nullable=true)
     */
    protected $rejectionAt;

    /**
     * @ORM\Column(name="viewed_at", type="datetime", nullable=true)
     */
    protected $viewedAt;
}
