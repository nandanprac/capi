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
class PatientAdditionalInfo extends ConsultEntity{

    /**
     * @Column(type="integer")
     */
    protected $user_id;

    protected $allergies;

    protected $medication;

    protected $prevDiagnosedConditions;

    protected $additionalDetails;

}