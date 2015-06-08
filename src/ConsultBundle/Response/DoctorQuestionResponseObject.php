<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 05/06/15
 * Time: 13:03
 */

namespace ConsultBundle\Response;

use ConsultBundle\Entity\UserInfo;

/**
 * Class DoctorQuestionResponseObject
 *
 * @package ConsultBundle\Response
 */
class DoctorQuestionResponseObject extends DetailQuestionResponseObject
{
    /**
     * @var array $images
     */
    private $images;

    /**
     * @var UserInfo $patientInfo
     */
    private $patientInfo;

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param array $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return UserInfo
     */
    public function getPatientInfo()
    {
        return $this->patientInfo;
    }

    /**
     * @param UserInfo $patientInfo
     */
    public function setPatientInfo($patientInfo)
    {
        $this->patientInfo = $patientInfo;
    }

}