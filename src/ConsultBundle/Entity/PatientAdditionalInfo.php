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
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\PatientAdditionalInfoRepository")
 * @ORM\Table(name="patient_additional_info")
 * @ORM\HasLifecycleCallbacks()
 */
class PatientAdditionalInfo extends BaseEntity
{
    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;
    /**
     * @ORM\Column(type="text")
     */
    protected $allergies;

    /**
     * @ORM\Column(type="text")
     */
    protected $medication;

    /**
     * @ORM\Column(name="prev_diagnosed_conditions", type="text")
     */
    protected $prevDiagnosedConditions;


    /**
     * @ORM\Column(name="additional_details", type="text")
     */
    protected $additionalDetails;

}