<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 15/07/15
 * Time: 15:44
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\AccessType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="sub_specialities")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @AccessType("public_methods")
 */
class SubSpeciality extends BaseEntity
{
    /**
     * @var Speciality $speciality
     *
     * @ORM\ManyToOne(targetEntity = "Speciality", inversedBy="subSpecialities")
     * @ORM\JoinColumn(name = "speciality_id", referencedColumnName = "id")
     */
    private $speciality;

    /**
     * @ORM\Column(name="sub_speciality", type="string", length=322)
     * @Assert\NotBlank()
     */
    private $subSpeciality;

    /**
     * @return mixed
     */
    public function getSpeciality()
    {
        return $this->speciality;
    }

    /**
     * @param mixed $speciality
     */
    public function setSpeciality($speciality)
    {
        $this->speciality = $speciality;
    }

    /**
     * @return mixed
     */
    public function getSubSpeciality()
    {
        return $this->subSpeciality;
    }

    /**
     * @param mixed $subSpeciality
     */
    public function setSubSpeciality($subSpeciality)
    {
        $this->subSpeciality = trim($subSpeciality);
    }
}
