<?php
/**
 * @author Anshuman
 * Date: 29/04/15
 * Time: 13:25
 */

namespace ConsultORMBundle\Entity;


class ConsultEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime" name="created_dttm")
     */
    protected $createdDttm;

    /**
     * @ORM\Column(type="datetime" name="modified_dttm")
     */
    protected $modifiedDttm;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->$createdDttm = new \DateTime();
        $this->modifiedDttm = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setModifiedDate()
    {
        $this->modifiedDttm = new \DateTime();
    }

}