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
class BasicPatientInfoResponse extends ConsultResponseObject
{
    /**
     * @var float
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
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param float $age
     */
    public function setAge($age)
    {
        $this->age = $this->getFloat($age);
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
     * @return location
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
