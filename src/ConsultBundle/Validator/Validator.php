<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 14:42
 */

namespace ConsultBundle\Validator;


use ConsultBundle\Entity\BaseEntity;

interface Validator {

    public function validate(BaseEntity $baseEntity);


}