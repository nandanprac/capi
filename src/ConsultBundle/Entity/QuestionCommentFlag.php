<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * QuestionCommentFlag
 *
 * @ORM\Table(name="questions_comments_flag")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionCommentFlag extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="ConsultBundle\Entity\QuestionComment")
     * @ORM\JoinColumn(name="question_comment_id", referencedColumnName="id", nullable = false)
     */
    private $questionComment;


    /**
     * @var string
     *
     * @ORM\Column(name="flag_code", type="string")
     *
     * @Assert\Choice(choices = {"IAP","SPM","ABV", "ADV", "OTH"}, message="Not a valid flag code")
     * @Assert\NotBlank
     */
    private $flagCode;

    /**
     * @var string
     *
     * @ORM\Column(name="flag_text", type="string", nullable=true)
     *
     */
    private $flagText;

    /**
     * @var integer
     *
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    private $practoAccountId;


    /**
     * Set flag text
     *
     * @param string $flagText
     */
    public function setFlagText($flagText)
    {
        $this->flagText = $flagText;
    }

    /**
     * Get flag text
     *
     * @return string
     */
    public function getFlagText()
    {
        return $this->flagText;
    }

    /**
     * Set flag code
     *
     * @param string $flagCode
     */
    public function setFlagCode($flagCode)
    {
        $this->flagCode = $flagCode;
    }

    /**
     * Get flag code
     *
     * @return string
     */
    public function getFlagCode()
    {
        return $this->flagCode;
    }

    /**
     * Set practoAccountId
     *
     * @param integer $practoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $practoAccountId;
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
     * @return QuestionComment
     */
    public function getQuestionComment()
    {
        return $this->questionComment;
    }

    /**
     * @param QuestionComment $questionComment
     */
    public function setQuestionComment($questionComment)
    {
        $this->questionComment = $questionComment;
    }
}
