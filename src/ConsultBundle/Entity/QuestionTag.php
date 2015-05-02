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
    protected $question;

    /**
     * @ORM\Column(type="string", length=127, name="tag")
     */    
    protected $tag;

    /**
     * @ORM\Column(type="smallint", name="user_defined")
     */
    protected $userDefined = 0;

    /**
     * Set Question
     *
     * @param Question $question - Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * Get Question
     *
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Get QuestionId
     *
     * @return integer
     */
    public function getQuestionId()
    {
        if ($this->question) {
            return $this->question->getId();
        }

        return null;
    }

    /**
     * Get Tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set Tag
     *
     * @param string $tag - Tag
     */
    public function setTag($tag)
    {
        $this->setString('tag', $tag);
    }

    /**
     * Is User Defined
     *
     * @return boolean
     */
    public function isUserDefined()
    {
        return $this->userDefined;
    }

    /**
     * Set User Defined
     *
     * @param boolean $userDefined - User Defined
     */
    public function setUserDefined($userDefined)
    {
        $this->setBoolean('userDefined', $userDefined);
    }

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
}
