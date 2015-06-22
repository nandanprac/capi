<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 22/05/15
 * Time: 17:13
 */

namespace ConsultBundle\Entity;


class DoctorEntity
{

    private $name;

    /**
     * @var string
     */
    private $speciality;

    private $profilePicture;

<<<<<<< HEAD
    /**
     * @param null   $name
     * @param null   $speciality
     * @param string $profilePicture
     */
    public function __construct(
        $name = null,
        $speciality = null,
        $profilePicture = ''
    ) {
=======
    public function __construct($name=null, $specialty=null,
        $profilePicture = 'http://www.1stdoctor.com/wp-content/uploads/2013/11/woman_doctor_02.png')
    {
>>>>>>> master
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
}
