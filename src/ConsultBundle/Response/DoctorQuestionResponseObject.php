<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 05/06/15
 * Time: 13:03
 */

namespace ConsultBundle\Response;

use ConsultBundle\Entity\DoctorQuestion;
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
     * @param \ConsultBundle\Entity\DoctorQuestion $doctorQuestion
     */
    public function __construct(DoctorQuestion $doctorQuestion)
    {
        parent::__construct($doctorQuestion->getQuestion());
        $this->setId($doctorQuestion->getId());
        $this->setState($doctorQuestion->getState());
        //$this->images = $doctorQuestion->getQuestion()->getImages();
    }

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

    protected function populatePatientInfo(UserInfo $userInfo, $practoAccountId = null)
    {
        $patientInfo = new DetailPatientInfoResponse();
        $patientInfo->setAllergies($userInfo->getAllergies());
        $patientInfo->setMedications($userInfo->getMedications());
        $patientInfo->setPrevDiagnosedConditions($userInfo->getPrevDiagnosedConditions());
        $patientInfo->setHeightInCms($userInfo->getHeightInCms());
        $patientInfo->setWeightInKgs($userInfo->getWeightInKgs());
        $patientInfo->setBloodGroup($userInfo->getBloodGroup());
        $patientInfo->setAge($userInfo->getAge());
        $patientInfo->setGender($userInfo->getGender());
        $patientInfo->setOccupation($userInfo->getOccupation());
        $patientInfo->setLocation($userInfo->getLocation());
        $patientInfo->setAllergyStatus($userInfo->getAllergyStatus());
        $patientInfo->setPrevDiagnosedConditionsStatus($userInfo->getDiagnosedConditionStatus());
        $patientInfo->setMedicationStatus($userInfo->getMedicationStatus());
        $this->setPatientInfo($patientInfo);

    }

}
