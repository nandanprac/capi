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
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionBookmarkRepository")
 * @ORM\Table(name="question_bookmarks")
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionBookmark extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy ="bookmarks")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $questions;

    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     */
    protected $practoAccountId;

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
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * @param mixed $practoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $practoAccountId;
    }


}
