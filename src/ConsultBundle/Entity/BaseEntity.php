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
     * Set attributes from snake_cased array of key-value pairs
     *
     * @param array $attributes - Attributes
     *
     * @return boolean
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $attrSnake => $value) {
            //if ($this->isEditableAttribute($attrSnake)) {
                $attrCamel = str_replace(' ', '', ucwords(str_replace('_', ' ', $attrSnake)));
                $setter = 'set' . $attrCamel;
                try {
                    if ('' === $value) {
                        $value = null;
                    }
                    $this->$setter($value);
                } catch (NumberParseException $e) {
                    throw new ValidationError(array('mobile' => array('This value is not a valid mobile number')));
                } catch (BadAttributeException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    throw new BadAttributeException($attrSnake);
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
}
