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

    private $specialty;

    private $profilePicture;

    public function __construct($name=null, $specialty=null,
        $profilePicture = 'http://www.1stdoctor.com/wp-content/uploads/2013/11/woman_doctor_02.png'
    ) {
        $this->name = $name;
        $this->specialty = $specialty;
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
     * @return mixed
     */
    public function getSpecialty()
    {
        return $this->specialty;
    }

    /**
     * @param mixed $specialty
     */
    public function setSpecialty($specialty)
    {
        $this->specialty = $specialty;
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
