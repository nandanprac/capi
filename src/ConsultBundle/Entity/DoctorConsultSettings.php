<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="doctor_consult_settings")
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorConsultSettings extends BaseEntity
{

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_picture", type="string", length=255, nullable=true)
     */
    private $profilePicture;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $location;

    /**
     * @ORM\Column(name="practo_account_id", type="integer")
     * @Assert\NotBlank()
     */
    private $practoAccountId;

    /**
     * @ORM\Column(name="fabric_doctor_id", type="integer")
     * @Assert\NotBlank()
     */
    private $fabricDoctorId;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=16)
     * @Assert\NotBlank()
     */
    private $timezone = "Asia/Calcutta";

    /**
     * @var integer
     *
     * @ORM\Column(name="num_ques_day", type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $numQuesDay;

    /**
     * @var integer
     *
     * @ORM\Column(name="preferred_consultation_timings", type="integer", nullable=true)
     */
    private $preferredConsultationTimings;

    /**
     * @var integer
     *
     * @ORM\Column(name="consultation_days", type="integer", nullable=true)
     */
    private $consultationDays;

    /**
     * @var string
     *
     * @ORM\Column(name="speciality", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $speciality;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_activated", type="boolean")
     */
    private $activated = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="consent_given", type="boolean")
     */
    private $consentGiven = false;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=16, nullable=true)
     */
    private $status;


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
     * Set name
     *
     * @param string $name
     * @return DoctorConsultSettings
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set profilePicture
     *
     * @param string $profilePicture
     *
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
    }

    /**
     * Get profilePicture
     *
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return DoctorConsultSettings
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return DoctorConsultSettings
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set numQuesDay
     *
     * @param integer $numQuesDay
     * @return DoctorConsultSettings
     */
    public function setNumQuesDay($numQuesDay)
    {
        $this->numQuesDay = $numQuesDay;

        return $this;
    }

    /**
     * Get numQuesDay
     *
     * @return integer
     */
    public function getNumQuesDay()
    {
        return $this->numQuesDay;
    }

    /**
     * Set preferredConsultationTimings
     *
     * @param integer $preferredConsultationTimings
     * @return DoctorConsultSettings
     */
    public function setPreferredConsultationTimings($preferredConsultationTimings)
    {
        $this->preferredConsultationTimings = $preferredConsultationTimings;

        return $this;
    }

    /**
     * Get preferredConsultationTimings
     *
     * @return integer
     */
    public function getPreferredConsultationTimings()
    {
        return $this->preferredConsultationTimings;
    }

    /**
     * Set consultationDays
     *
     * @param integer $consultationDays
     * @return DoctorConsultSettings
     */
    public function setConsultationDays($consultationDays)
    {
        $this->consultationDays = $consultationDays;

        return $this;
    }

    /**
     * Get consultationDays
     *
     * @return integer
     */
    public function getConsultationDays()
    {
        return $this->consultationDays;
    }

    /**
     * Set speciality
     *
     * @param string $speciality
     * @return DoctorConsultSettings
     */
    public function setSpeciality($speciality)
    {
        $this->speciality = $speciality;

        return $this;
    }

    /**
     * Get speciality
     *
     * @return string
     */
    public function getSpeciality()
    {
        return $this->speciality;
    }

    /**
     * @return boolean
     */
    public function isActivated()
    {
        return $this->activated;
    }

    /**
     * @param boolean $activated
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;
    }

    /**
     * @return boolean
     */
    public function isConsentGiven()
    {
        return $this->consentGiven;
    }

    /**
     * @param boolean $consentGiven
     */
    public function setConsentGiven($consentGiven)
    {
        $this->consentGiven = $consentGiven;
    }





    /**
     * Set status
     *
     * @param string $status
     * @return DoctorConsultSettings
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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

    /**
     * @return mixed
     */
    public function getFabricDoctorId()
    {
        return $this->fabricDoctorId;
    }

    /**
     * @param mixed $fabricDoctorId
     */
    public function setFabricDoctorId($fabricDoctorId)
    {
        $this->fabricDoctorId = $fabricDoctorId;
    }
}

