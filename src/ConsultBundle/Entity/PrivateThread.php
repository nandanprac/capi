<?php

namespace ConsultBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\PrivateThreadRepository")
 * @ORM\Table(name="private_thread")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class PrivateThread extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="ConsultBundle\Entity\UserInfo", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_info_id", referencedColumnName="id", nullable = false)
     */
    private $userInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=32)
     * @Assert\NotBlank()
     */
    private $subject;

    /**
     * @var integer
     *
     * @ORM\Column(name="doctor_id", type="integer")
     * @Assert\NotBlank()
     */
    private $doctorId;

    /**
     * @ORM\ManyToOne(targetEntity = "Question")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id", nullable = false)
     */
    protected $question;

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return integer
     */
    public function getDoctorId()
    {
        return $this->doctorId;
    }

    /**
     * @param integer $doctorId
     */
    public function setDoctorId($doctorId)
    {
        $this->doctorId = $doctorId;
    }


    /**
     * get User object
     * @return User
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Set UserInfo object
     * @param UserInfo $userInfo - UserInfo object
     */
    public function setUserInfo(UserInfo $userInfo)
    {
        $this->userInfo = $userInfo;
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

}
