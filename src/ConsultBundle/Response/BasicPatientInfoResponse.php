<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 10/06/15
 * Time: 19:48
 */

namespace ConsultBundle\Response;

/**
 * Class BasicPatientInfoResponse
 *
 * @package ConsultBundle\Response
 */
class BasicPatientInfoResponse
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $age;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var string
     */
    private $location;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

//    /**
//     * @params string $name
//     */
    public function setName($name)
    {
        $this->name=$name;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }
}
