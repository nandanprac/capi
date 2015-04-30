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
 * @ORM\Entity(repositoryClass="ConsultORMBundle\Repository\QuestionImagesRepository")
 * @ORM\Table(name=Question_Images)
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionImages extends ConsultEntity{


    /**
     * @ORM\ManyToOne(targetEntity = "QuestionEntity", inversedBy ="doctorQuestions")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $questions;

}