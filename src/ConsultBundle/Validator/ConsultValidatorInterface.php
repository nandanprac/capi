<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 14:42
 */

namespace ConsultBundle\Validator;

use ConsultBundle\Entity\BaseEntity;

/**
 * Interface ConsultValidatorInterface
 *
 * @package ConsultBundle\Validator
 */
interface ConsultValidatorInterface
{

    /**
     * @param \ConsultBundle\Entity\BaseEntity $baseEntity
     *
     * @return mixed
     */
    public function validate(BaseEntity $baseEntity);
}
