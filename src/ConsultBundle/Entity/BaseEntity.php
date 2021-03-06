<?php
/**
 * @author Anshuman
 * Date: 29/04/15
 * Time: 13:25
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\RestBundle\Util\Codes;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Symfony\Component\Validator\Constraints as Assert;
use ConsultBundle\Manager\ValidationError;

/**
 * ConsultBundle\Entity\BaseEntity
 *
 * @ORM\MappedSuperclass()
 * @ExclusionPolicy("all")
 */
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
     * @Exclude()
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
     * @param array $attributes
     * @return bool
     * @throws ValidationError
     * @throws \Exception
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $attrSnake => $value) {
            //if ($this->isEditableAttribute($attrSnake)) {
            $attrCamel = str_replace(' ', '', ucwords(str_replace('_', ' ', $attrSnake)));
            $setter = 'set'.$attrCamel;
            try {
                if ('' === $value) {
                    $value = null;
                }
                if (method_exists($this, $setter)) {
                    $this->$setter($value);
                }

            } catch (\Exception $e) {
                throw new \HttpException($attrCamel."is not a valid field in ".__CLASS__, Codes::HTTP_BAD_REQUEST);
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
        } elseif (is_numeric($value)) {
            $this->$field = (bool) $value;
        } elseif (null === $value || '' === $value) {
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

        return false;
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
        } elseif (!empty($value)) {
            $this->$field = new \DateTime($value);
        } else {
            $this->$field = null;
        }
    }

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


}
