<?php
/**
 * @author Anshuman
 * Date: 29/04/15
 * Time: 13:25
 */

namespace ConsultBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


class BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="modified_at")
     */
    protected $modifiedAt;


    /**
     * @ORM\Column(type="smallint", name="soft_delete")
     */
    protected $softDelete = 0;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
        $this->modifiedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setModifiedDate()
    {
        $this->modifiedAt = new \DateTime();
    }
}
