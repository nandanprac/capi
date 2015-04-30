<?php
/**
 * @author Anshuman
 * Date: 29/04/15
 * Time: 13:36
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionViewsRepository")
 * @ORM\Table(name="question_views")
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionViews extends BaseEntity{


    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy ="doctorQuestions")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $questions;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;



}