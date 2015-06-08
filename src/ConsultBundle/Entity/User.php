<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User  NOT IN USE
 *
 * @package ConsultBundle\Entity
 */
class User extends UserInfo
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
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = new \DateTime($dateOfBirth);
        $this->dateOfBirth->format('Y-m-d');
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set gender
     *
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set blood group
     *
     * @param string $bloodGroup - Blood Group
     */
    public function setBloodGroup($bloodGroup)
    {
        $this->bloodGroup = $bloodGroup;
    }

    /**
     * Get blood group
     *
     * @return string
     */
    public function getBloodGroup()
    {
        return $this->bloodGroup;
    }

    /**
     * Set height
     *
     * @param float $height - Height
     */
    public function setHeightInCms($height)
    {
        $this->heightInCms = $height;
    }

    /**
     * Get height
     *
     * @return float
     */
    public function getHeightInCms()
    {
        return $this->heightInCms;
    }

    /**
     * Set weight
     *
     * @param float $weight - Weight
     */
    public function setWeightInKgs($weight)
    {
        $this->weightInKgs = $weight;
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeightInKgs()
    {
        return $this->weightInKgs;
    }
}
