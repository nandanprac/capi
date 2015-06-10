<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 05/06/15
 * Time: 12:42
 */

namespace ConsultBundle\Response;

use ConsultBundle\Entity\BaseEntity;
use FOS\RestBundle\Util\Codes;

/**
 * Class AbstractResponseObject
 *
 * @package ConsultBundle\Response
 */
class ConsultResponseObject
{
    protected $id;

    protected $createdAt;

    protected $modifiedAt;

    /**
     * @param \ConsultBundle\Entity\BaseEntity $baseEntity
     */
    public function __construct(BaseEntity $baseEntity = null)
    {
        if (!is_null(($baseEntity))) {
            $this->setId($baseEntity->getId());
            $this->setModifiedAt($baseEntity->getModifiedAt());
            $this->setCreatedAt($baseEntity->getCreatedAt());
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param mixed $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @param array $attributes
     *
     * @throws \HttpException
     */
    public function setAttributes(array $attributes)
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

                } else {
                    throw new \HttpException($attrCamel."is not a valid field in".__CLASS__, Codes::HTTP_INTERNAL_SERVER_ERROR);
                }

            } catch (\Exception $e) {
                throw new \HttpException($attrCamel."is not a valid field in ".__CLASS__, Codes::HTTP_BAD_REQUEST);
            }
            //} else {
            //    throw new BadAttributeException($attrSnake);
            //}
        }
    }

    /**
     * @param $value
     *
     * Just a placeholder
     */
    public function setSoftDeleted($value)
    {
        //Do Nothing
    }




}