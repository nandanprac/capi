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
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorNotificationRepository")
 * @ORM\Table(name="doctor_notifications")
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorNotification extends BaseEntity
{
    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     */
    protected $practoAccountId;

    /**
     * @ORM\Column(type="integer", name="question_id")
     */
    protected $question_id;

    /**
     * @ORM\Column(name="text", type="text")
     */
    protected $text;

    /**
     * @ORM\Column(type="smallint", name="viewed")
     */
    protected $viewed=0;

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
     * @param $question_id
     */
    public function setQuestionId($question_id)
    {
        $this->question_id = $question_id;
    }

    /**
     * @return mixed
     */
    public function getQuestionId()
    {
        return $this->question_id;
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
