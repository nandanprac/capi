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
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionTagRepository")
 * @ORM\Table(name="question_tags")
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionTag extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy = "doctorQuestions")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $questions;

    /**
     * @ORM\Column(type="string", length=127, name="tag")
     */    
    protected $tag;

    /**
     * @ORM\Column(type="smallint", name="is_user_defined")
     */
    protected $isUserDefined = 0;

    /**
     * @return mixed
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param mixed $questions
     */
    public function setQuestions($questions)
    {
        $this->questions = $questions;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return mixed
     */
    public function getIsUserDefined()
    {
        return $this->isUserDefined;
    }

    /**
     * @param mixed $isUserDefined
     */
    public function setIsUserDefined($isUserDefined)
    {
        $this->isUserDefined = $isUserDefined;
    }



}
