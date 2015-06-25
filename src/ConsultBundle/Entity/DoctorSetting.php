<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorSetting
 *
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorRepository")
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorSetting extends BaseEntity
{
}
