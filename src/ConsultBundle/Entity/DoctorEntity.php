<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 22/05/15
 * Time: 17:13
 */

namespace ConsultBundle\Entity;

/**
 * Class DoctorEntity
 *
 * @package ConsultBundle\Entity
 */
class DoctorEntity
{

    private $name;

    private $fabricId;

    /**
     * @var string
     */
    private $speciality;

    private $profilePicture;

    /**
     * @param null   $name
     * @param null   $speciality
     * @param string $profilePicture
     */
    public function __construct(
        $name = null,
        $speciality = null,
        $profilePicture = ''
    )
    {
        $this->name = $name;
        $this->speciality = $speciality;
        $this->profilePicture = $profilePicture;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSpeciality()
    {
        return $this->speciality;
    }

    /**
     * @param string $speciality
     */
    public function setSpeciality($speciality)
    {
        $this->speciality = $speciality;
    }


    /**
     * @return mixed
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @param mixed $profilePicture
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
    }

    /**
     * @return mixed
     */
    public function getFabricId()
    {
        return $this->fabricId;
    }

    /**
     * @param mixed $fabricId
     */
    public function setFabricId($fabricId)
    {
        $this->fabricId = $fabricId;
    }


}
