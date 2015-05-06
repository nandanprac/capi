<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:18
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="keep")
 */
class Keep
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="keep")
     */
    protected $keep;

    /**
     * Get PractoAccountId
     *
     * @return integer
     */
    public function getKeep()
    {
        return $this->key;
    }

    /**
     * Set Key
     *
     * @param string $key
     */
    public function setKeep($keep)
    {
        $this->keep = $keep;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
