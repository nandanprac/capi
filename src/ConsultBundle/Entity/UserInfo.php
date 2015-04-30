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
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\userInfoRepository")
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
     * @ORM\Column(type="text", name="allergies")
     */
    protected $allergies;

    /**
     * @ORM\Column(type="text", name="medication")
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