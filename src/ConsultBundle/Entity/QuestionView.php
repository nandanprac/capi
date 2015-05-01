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
class QuestionView extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy ="doctorQuestions")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $questions;

    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     */
    protected $practoAccountId;
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
     * Set practoAccountId
     *
     * @param integer $practoAccountId
     * @return QuestionView
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $practoAccountId;

        return $this;
    }

    /**
     * Get practoAccountId
     *
     * @return integer 
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
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
     * @return QuestionView
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
     * @return QuestionView
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
     * @return QuestionView
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
     * @return QuestionView
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
