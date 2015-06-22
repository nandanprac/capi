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
 * @ORM\Entity
 * @ORM\Table(name="question_tags")
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionTag extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity = "Question")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=127, name="tag")
<<<<<<< HEAD
     */
    private $tag;
=======
     */    
    protected $tag;
>>>>>>> master

    /**
     * @ORM\Column(type="smallint", name="user_defined")
     */
    private $userDefined = 0;

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
            return $this->getQuestion()->getId();
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
        $this->tag = $tag;
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
        $this->userDefined = $userDefined;
    }

    /**
     * @return Question
     */
    public function getQuestions()
    {
        return $this->question;
    }

    /**
     * @param \ConsultBundle\Entity\Question $question
     */
    public function setQuestions(Question $question)
    {
        $this->question = $question;
    }
}
