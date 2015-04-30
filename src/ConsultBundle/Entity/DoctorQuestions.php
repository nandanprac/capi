<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 13:34
 */

namespace ConsultORMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="ConsultORMBundle\Repository\DoctorQuestionsRepository")
 * @ORM\Table(name=Doctor_Question)
 */
class DoctorQuestions extends BaseEntity {

    /**
     * @ORM\ManyToOne(targetEntity = "QuestionEntity", inversedBy ="doctorQuestions")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
protected $question;
    /**
     * @ORM\OnetoOne(targetEntity = "Reply", mappedBy = "doctorQuestion")
     */
protected $reply;
    /**
     * @ORM\Column(type="integer")
     */
    protected $doctor_id;

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