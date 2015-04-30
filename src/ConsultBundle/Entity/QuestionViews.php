<?php
/**
 * @author Anshuman
 * Date: 29/04/15
 * Time: 13:36
 */

namespace ConsultORMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultORMBundle\Repository\QuestionViewsRepository")
 * @ORM\Table(name=Question_Views)
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionViews extends ConsultEntity{


    /**
     * @ORM\ManyToOne(targetEntity = "QuestionEntity", inversedBy ="doctorQuestions")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $questions;

}