<?php
/**
 * @author Anshuman
 * Date: 29/04/15
 * Time: 13:25
 */

namespace ConsultBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * ConsultBundle\Entity\BaseEntity
 *
 * @ORM\MappedSuperclass
 */
abstract class BaseEntity
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
     * @ORM\Column(type="smallint", name="soft_deleted")
     */
    protected $softDeleted = 0;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @return null
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        $this->modifiedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setModifiedAt()
    {
        $this->modifiedAt = new \DateTime();
    }

    /**
     * Set softDeleted
     *
     * @param boolean $softDeleted - Soft Deleted
     *
     * @return boolean
     */
    public function setSoftDeleted($softDeleted)
    {

        $this->setBoolean('softDeleted', $softDeleted);
    }

    /**
     * Is softDeleted
     *
     * @return boolean
     */
    public function isSoftDeleted()
    {
        return $this->softDeleted;
    }

    /**
     * @param $attributes
     * @return bool
     * @throws BadAttributeException
     * @throws ValidationError
     * @throws \Exception
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $attrSnake => $value) {
            //if ($this->isEditableAttribute($attrSnake)) {
<<<<<<< HEAD
            $attrCamel = str_replace(' ', '', ucwords(str_replace('_', ' ', $attrSnake)));
            $setter = 'set'.$attrCamel;
            try {
                if ('' === $value) {
                    $value = null;
                }
                if (method_exists($this, $setter)) {
                    $this->$setter($value);

=======
                $attrCamel = str_replace(' ', '', ucwords(str_replace('_', ' ', $attrSnake)));
                $setter = 'set' . $attrCamel;
                try {
                    if ('' === $value) {
                        $value = null;
                    }
                   if(method_exists($this, $setter))
                   {
                       $this->$setter($value);

                   }

                } catch (\Exception $e) {
                    var_dump($attrCamel);die;
                    throw new \HttpException($attrCamel. "is not a valid field in ".__CLASS__ ,Codes::HTTP_BAD_REQUEST);
>>>>>>> master
                }
            //} else {
            //    throw new BadAttributeException($attrSnake);
            //}
        }

        return true;
    }

    /**
     * Set field as Boolean (Helper)
     *
     * @param string $field - field name
     * @param mixed  $value - string
     */
    public function setBoolean($field, $value)
    {

        if (is_bool($value)) {
            $this->$field = $value;
        } else if (is_numeric($value)) {

            $this->$field = (bool) $value;
        } else if (null === $value || '' === $value) {
            $this->$field = null;
        } else {
            $this->$field = ('true' === $value);
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool|null
     */
    public static function toBool($value)
    {
        if (is_bool($value)) {
            return $value;
        } elseif (is_numeric($value)) {
            return (bool) $value;
        } elseif (null === $value || '' === $value) {
            return null;
        } else {
            return ('true' === $value);
        }
    }

    /**
     * Set Integer
     *
     * @param string $field - Field Name
     * @param mixed  $value - Value
     */
    public function setInt($field, $value)
    {
        if (is_numeric($value)) {
            $this->$field = intval($value);
        } else {
            $this->$field = $value;
        }
    }

    /**
     * Set String
     *
     * @param string $field - field name
     * @param mixed  $value - Value
     */
    public function setString($field, $value)
    {
        if (is_string($value)) {
            $this->$field = trim($value);
        } else {
            $this->$field = $value;
        }
    }

    /**
     * Set DateTime field (Helper)
     *
     * @param string $field - field name
     * @param mixed  $value - string or DateTime object
     */
    public function setDateTime($field, $value)
    {
        if ($value instanceof \DateTime) {
            $this->$field = $value;
        } else if (!empty($value)) {
            $this->$field = new \DateTime($value);
        } else {
            $this->$field = null;
        }
    }

<<<<<<< HEAD
    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function getDateTimeStr(\DateTime $dateTime)
    {
        if (empty($dateTime)) {
            $dateTime = new \DateTime();
        }

        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }


=======
>>>>>>> master
}
