<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:18
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\UserNotificationRepository")
 * @ORM\Table(name="patient_notifications")
 * @ORM\HasLifecycleCallbacks()
 */
class UserNotification extends BaseEntity
{
    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     */
    protected $practoAccountId;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="userNotifications")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $question;

    /**
     * @ORM\Column(name="text", type="text")
     */
    protected $text;

    /**
     * @ORM\Column(type="smallint", name="viewed")
     */
    protected $viewed = 0;

    /**
     * Get PractoAccountId
     *
     * @return integer
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * Set PractoAccountId
     *
     * @param integer $practoAccountId - PractoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->setInt('practoAccountId', $practoAccountId);
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
            return $this->question->getId();
        }

        return null;
    }

    /**
     * Is Viewed
     *
     * @return boolean
     */
    public function isViewed()
    {
        return $this->viewed;
    }

    /**
     * Set viewed
     *
     * @param boolean $viewed - Viewed
     */
    public function setViewed($viewed)
    {
        $this->setBoolean('viewed', $viewed);
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set Text
     *
     * @param string $text - Text
     */
    public function setText($text)
    {
        $this->setString('text', $text);
    }
}
