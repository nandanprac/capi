<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 08/07/15
 * Time: 17:40
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ConsultBundle\Entity\SubSpeciality;

/**
 * @ORM\Entity
 * @ORM\Table(name="speciality")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Speciality extends BaseEntity
{
    /**
     * @ORM\Column(name="name", type="string", length=32)
     */
    private $name;

    /**
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="SubSpeciality", mappedBy="speciality", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     */
    private $subSpecialities;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_activated", type="boolean")
     */
    private $activated = false;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return boolean
     */
    public function isActivated()
    {
        return $this->activated;
    }

    /**
     * @param boolean $activated
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;
    }
}
