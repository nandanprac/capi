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
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @var integer
     */
    private $softDeleted;


    /**
     * Set tag
     *
     * @param string $tag
     * @return QuestionTag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set isUserDefined
     *
     * @param integer $isUserDefined
     * @return QuestionTag
     */
    public function setIsUserDefined($isUserDefined)
    {
        $this->isUserDefined = $isUserDefined;

        return $this;
    }

    /**
     * Get isUserDefined
     *
     * @return integer 
     */
    public function getIsUserDefined()
    {
        return $this->isUserDefined;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return QuestionTag
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     * @return QuestionTag
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime 
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set softDeleted
     *
     * @param integer $softDeleted
     * @return QuestionTag
     */
    public function setSoftDeleted($softDeleted)
    {
        $this->softDeleted = $softDeleted;

        return $this;
    }

    /**
     * Get softDeleted
     *
     * @return integer 
     */
    public function getSoftDeleted()
    {
        return $this->softDeleted;
    }

    /**
     * Set questions
     *
     * @param \ConsultBundle\Entity\Question $questions
     * @return QuestionTag
     */
    public function setQuestions(\ConsultBundle\Entity\Question $questions = null)
    {
        $this->questions = $questions;

        return $this;
    }

    /**
     * Get questions
     *
     * @return \ConsultBundle\Entity\Question 
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}
