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
 * @ORM\Entity
 * @ORM\Table(name="user_info")
 * @ORM\HasLifecycleCallbacks()
 */
class UserInfo extends BaseEntity
{
    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     */
    protected $practoAccountId;
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
     * Get PractoAccountId
     *
     * @return integer
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * Set PractoAccountId
     *
     * @param integer $practoAccountId - PractoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->setInt('practoAccountId', $practoAccountId);
    }

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
     * @param string $medications - Allergies
     */
    public function setAllergies($allergies)
    {
        $this->setString('allergies', $allergies);
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
        $this->setString('medications', $medications);
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
        $this->setString('prevDiagnosedConditions', $prevDiagnosedConditions);
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
        $this->setString('additionalDetails', $additionalDetails);
    }
}
