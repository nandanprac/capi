<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseEntity
{

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_birth", type="datetime", nullable=true)
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
