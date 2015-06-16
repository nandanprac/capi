<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 13:34
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="question_images")
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionImage extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy ="doctorQuestions")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $question;




    /**
     * @ORM\Column(name="url", type="text", name="url")
     */
    protected $url;



    /**
     * Get Url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set Url
     *
     * @param string $url - URl
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

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
}
