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
    private $bloodGroup;

    /**
     * @var float
     */
    private $heightInCms;

    /**
     * @var float
     */
    private $weightInKgs;

    private $allergies = null;

    private $medications = null;

    private $prevDiagnosedConditions = null;

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
        $this->heightInCms = $heightInCms;
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
        $this->weightInKgs = $weightInKgs;
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
}
