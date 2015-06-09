<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:15
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_info")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class UserInfo extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    private $practoAccountId;

    /**
     * @var string
     *
     * @ORM\Column(name="relative_name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_relative", type="boolean")
     */
    private $isRelative=false;

    /**
     * @var integer
     *
     * @ORM\Column(name="age", type="integer", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=1, nullable=true)
     * @Assert\Choice(choices = {"M", "F"}, message="Input can only be M/F")
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="blood_group", type="string", length=5, nullable=true)
     */
    private $bloodGroup;

    /**
     * @var float
     *
     * @ORM\Column(name="height_in_cms", type="float", nullable=true)
     */
    private $heightInCms;

    /**
     * @var float
     *
     * @ORM\Column(name="weight_in_kgs", type="float", nullable=true)
     */
    private $weightInKgs;


    /**
     * @ORM\Column(type="text", name="allergies", nullable=true)
     */
    protected $allergies = null;

    /**
     * @ORM\Column(type="text", name="medications", nullable=true)
     */
    protected $medications = null;

    /**
     * @ORM\Column(name="prev_diagnosed_conditions", type="text", nullable=true)
     */
    protected $prevDiagnosedConditions = null;

    /**
     * @ORM\Column(name="additional_details", type="text", nullable=true)
     */
    protected $additionalDetails = null;


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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function isIsRelative()
    {
        return $this->isRelative;
    }

    /**
     * @param boolean $isRelative
     */
    public function setIsRelative($isRelative)
    {
        $this->isRelative = $isRelative;
    }

    /**
     * @return int
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param int $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getBloodGroup()
    {
        return $this->bloodGroup;
    }

    /**
     * @param string $bloodGroup
     */
    public function setBloodGroup($bloodGroup)
    {
        $this->bloodGroup = $bloodGroup;
    }

    /**
     * @return float
     */
    public function getHeightInCms()
    {
        return $this->heightInCms;
    }

    /**
     * @param float $heightInCms
     */
    public function setHeightInCms($heightInCms)
    {
        $this->heightInCms = $heightInCms;
    }

    /**
     * @return float
     */
    public function getWeightInKgs()
    {
        return $this->weightInKgs;
    }

    /**
     * @param float $weightInKgs
     */
    public function setWeightInKgs($weightInKgs)
    {
        $this->weightInKgs = $weightInKgs;
    }


    /**
     * Get allergies
     *
     * @return string
     */
    public function getAllergies()
    {
        return $this->allergies;
    }

    /**
     * Set Medication
     *
     * @param string $allergies - Allergies
     */
    public function setAllergies($allergies)
    {
        $this->allergies = $allergies;
    }

    /**
     * Get medications
     *
     * @return string
     */
    public function getMedications()
    {
        return $this->medications;
    }

    /**
     * Set Medications
     *
     * @param string $medications - Medications
     */
    public function setMedications($medications)
    {
        $this->medications = $medications;
    }

    /**
     * Get Prev Diagnosed Conditions
     *
     * @return string
     */
    public function getPrevDiagnosedConditions()
    {
        return $this->prevDiagnosedConditions;
    }

    /**
     * Set Prev Diagnosed Conditions
     *
     * @param string $prevDiagnosedConditions - Prev Diagnosed Conditions
     */
    public function setPrevDiagnosedConditions($prevDiagnosedConditions)
    {
        $this->prevDiagnosedConditions = $prevDiagnosedConditions;
    }

    /**
     * Get Additional Details
     *
     * @return string
     */
    public function getAdditionalDetails()
    {
        return $this->additionalDetails;
    }

    /**
     * Set Additional Details
     *
     * @param string $additionalDetails - Additional Details
     */
    public function setAdditionalDetails($additionalDetails)
    {
        $this->additionalDetails = $additionalDetails;
    }
}
