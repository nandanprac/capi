<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:15
 */

namespace ConsultORMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultORMBundle\Repository\PatientAdditionalInfoRepository")
 * @ORM\Table(name=Patient_Additional_Info)
 * @ORM\HasLifecycleCallbacks()
 */
class PatientAdditionalInfo extends BaseEntity{

    /**
     * @Column(type="integer")
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