<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/06/15
 * Time: 16:08
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DoctorReplyFlag
 *
 * @ORM\Table(name="doctor_replies_flag")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorReplyFlag extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="ConsultBundle\Entity\DoctorReply")
     * @ORM\JoinColumn(name="reply_id", referencedColumnName="id")
     */
    private $doctorReply;


    /**
     * @var string
     *
     * @ORM\Column(name="flag_code", type="string")
     *
     * @Assert\Choice(choices = {"IAP","NOH","OTH"}, message="Not a valid flag code")
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
     * @return mixed
     */
    public function getDoctorReply()
    {
        return $this->doctorReply;
    }

    /**
     * @param mixed $doctorReply
     */
    public function setDoctorReply($doctorReply)
    {
        $this->doctorReply = $doctorReply;
    }

    /**
     * @return string
     */
    public function getFlagCode()
    {
        return $this->flagCode;
    }

    /**
     * @param string $flagCode
     */
    public function setFlagCode($flagCode)
    {
        $this->flagCode = $flagCode;
    }

    /**
     * @return string
     */
    public function getFlagText()
    {
        return $this->flagText;
    }

    /**
     * @param string $flagText
     */
    public function setFlagText($flagText)
    {
        $this->flagText = $flagText;
    }

    /**
     * @return int
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * @param int $practoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $practoAccountId;
    }


}
