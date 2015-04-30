<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 13:35
 */

namespace ConsultORMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultORMBundle\Repository\QuestionTagsRepository")
 * @ORM\Table(name=Question_Tags)
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionTags extends BaseEntity{


    /**
     * @ORM\ManyToOne(targetEntity = "QuestionEntity", inversedBy ="doctorQuestions")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $questions;

    protected $tag;

    /**
     * @ORM\(type="smallint")
     */
    protected $isUserDefined =0;

}