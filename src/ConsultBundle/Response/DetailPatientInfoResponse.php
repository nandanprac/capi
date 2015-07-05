<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 10/06/15
 * Time: 19:49
 */

namespace ConsultBundle\Response;

/**
 * Class DetailPatientInfoResponse
 *
 * @package ConsultBundle\Response
 */
class DetailPatientInfoResponse extends BasicPatientInfoResponse
{
    /**
     * @var string
     */
    private $bloodGroup="";

    /**
     * @var float
     */
    private $heightInCms;

    /**
     * @var float
     */
    private $weightInKgs;

    private $allergies = "";

    private $allergyStatus;

    private $medications = "";

    private $medicationStatus;

    private $prevDiagnosedConditionsStatus;

    private $prevDiagnosedConditions = "";

    private $occupation;

    private $location;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $profilePicture;

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
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @param string $profilePicture
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
    }


    /**
     * @return mixed
     */
    public function getBloodGroup()
    {
        return $this->bloodGroup;
    }

    /**
     * @param mixed $bloodGroup
     */
    public function setBloodGroup($bloodGroup)
    {
        $this->bloodGroup = $bloodGroup;
    }

    /**
     * @return mixed
     */
    public function getHeightInCms()
    {
        return $this->heightInCms;
    }

    /**
     * @param mixed $heightInCms
     */
    public function setHeightInCms($heightInCms)
    {
        $this->heightInCms = $this->getFloat($heightInCms);
    }

    /**
     * @return mixed
     */
    public function getWeightInKgs()
    {
        return $this->weightInKgs;
    }

    /**
     * @param mixed $weightInKgs
     */
    public function setWeightInKgs($weightInKgs)
    {
        $this->weightInKgs = $this->getFloat($weightInKgs);
    }

    /**
     * @return null
     */
    public function getAllergies()
    {
        return $this->allergies;
    }

    /**
     * @param null $allergies
     */
    public function setAllergies($allergies)
    {
        $this->allergies = $allergies;
    }

    /**
     * @return null
     */
    public function getMedications()
    {
        return $this->medications;
    }

    /**
     * @param null $medications
     */
    public function setMedications($medications)
    {
        $this->medications = $medications;
    }

    /**
     * @return null
     */
    public function getPrevDiagnosedConditions()
    {
        return $this->prevDiagnosedConditions;
    }

    /**
     * @param null $prevDiagnosedConditions
     */
    public function setPrevDiagnosedConditions($prevDiagnosedConditions)
    {
        $this->prevDiagnosedConditions = $prevDiagnosedConditions;
    }

    /**
     * @return mixed
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * @param mixed $occupation
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getAllergyStatus()
    {
        return $this->allergyStatus;
    }

    /**
     * @param mixed $allergyStatus
     */
    public function setAllergyStatus($allergyStatus)
    {
        $this->allergyStatus = $allergyStatus;
    }

    /**
     * @return mixed
     */
    public function getMedicationStatus()
    {
        return $this->medicationStatus;
    }

    /**
     * @param mixed $medicationStatus
     */
    public function setMedicationStatus($medicationStatus)
    {
        $this->medicationStatus = $medicationStatus;
    }

    /**
     * @return mixed
     */
    public function getPrevDiagnosedConditionsStatus()
    {
        return $this->prevDiagnosedConditionsStatus;
    }

    /**
     * @param mixed $prevDiagnosedConditionsStatus
     */
    public function setPrevDiagnosedConditionsStatus($prevDiagnosedConditionsStatus)
    {
        $this->prevDiagnosedConditionsStatus = $prevDiagnosedConditionsStatus;
    }
}




