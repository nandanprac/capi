<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 16:50
 */

namespace ConsultBundle\Manager;

use ConsultBundle\Helper;
use ConsultBundle\Validator\Validator;


abstract class BaseManager {
     protected $helper;
     protected $validator;

    /**
     * @param  $validator
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param  $helper
     */
    public function setHelper(Helper $helper)
    {
        $this->helper = $helper;
    }


    protected function validate($entity)
    {
        $this->validator.validate($entity);
    }
}