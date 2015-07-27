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
        $this->age = $this->getInt($age);
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
