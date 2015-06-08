<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:15
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
class UserInfo extends BaseEntity
{
    /**
     * @ORM\Column(type="text", name="allergies", nullable=true)
     */
    protected $allergies = null;

    /**
     * @ORM\Column(type="text", name="medications", nullable=true)
     */
    protected $medications = null;

    /**
     * @ORM\Column(name="prev_diagnosed_conditions", type="text", nullable=true)
     */
    protected $prevDiagnosedConditions = null;

    /**
     * @ORM\Column(name="additional_details", type="text", nullable=true)
     */
    protected $additionalDetails = null;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $userProfileDetails;


    /**
     * Get allergies
     *
     * @return string
     */
    public function getAllergies()
    {
        return $this->allergies;
    }

    /**
     * Set Medication
     *
     * @param string $allergies - Allergies
     */
    public function setAllergies($allergies)
    {
        $this->allergies = $allergies;
    }

    /**
     * Get medications
     *
     * @return string
     */
    public function getMedications()
    {
        return $this->medications;
    }

    /**
     * Set Medications
     *
     * @param string $medications - Medications
     */
    public function setMedications($medications)
    {
        $this->medications = $medications;
    }

    /**
     * Get Prev Diagnosed Conditions
     *
     * @return string
     */
    public function getPrevDiagnosedConditions()
    {
        return $this->prevDiagnosedConditions;
    }

    /**
     * Set Prev Diagnosed Conditions
     *
     * @param string $prevDiagnosedConditions - Prev Diagnosed Conditions
     */
    public function setPrevDiagnosedConditions($prevDiagnosedConditions)
    {
        $this->prevDiagnosedConditions = $prevDiagnosedConditions;
    }

    /**
     * Get Additional Details
     *
     * @return string
     */
    public function getAdditionalDetails()
    {
        return $this->additionalDetails;
    }

    /**
     * Set Additional Details
     *
     * @param string $additionalDetails - Additional Details
     */
    public function setAdditionalDetails($additionalDetails)
    {
        $this->additionalDetails = $additionalDetails;
    }
}
