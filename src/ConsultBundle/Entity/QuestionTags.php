<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 13:35
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionTagsRepository")
 * @ORM\Table(name="question_tags")
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionTags extends BaseEntity{


    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy ="doctorQuestions")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $questions;

    /**
     * @ORM\Column(type="string")
     */    
    protected $tag;

    /**
     * @ORM\Column(type="smallint", name="is_user_defined")
     */
    protected $isUserDefined =0;

}