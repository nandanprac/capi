<?php

namespace ConsultBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Admin
 *
 * @ORM\Table(name="admin_users")
 * @ORM\Entity
 */
class Admin extends BaseEntity
{
    /**

     * @ORM\ManyToMany(targetEntity="Permission")
     * @ORM\JoinTable(name="admin_user_permission",
     *      joinColumns={@ORM\JoinColumn(name="admin_user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
     *      )
     */
    private $permissions;

    /**
    * @var integer
    *
    * @ORM\Column(name="practo_account_id", type="integer", unique=true)
    */
    private $practoAccountId;


    /**
     * Set practoAccountId
     *
     * @param integer $practoAccountId
     * @return Admin
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $practoAccountId;

        return $this;
    }

    /**
     * Get practoAccountId
     *
     * @return integer 
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }
}
