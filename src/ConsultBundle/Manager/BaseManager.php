<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 16:50
 */

namespace ConsultBundle\Manager;

use ConsultBundle\Helper\Helper;
use ConsultBundle\Validator\Validator;


abstract class BaseManager {

    /**
     * @var Helper $helper
     */
    protected $helper;

    /**
     * validator
     * @var Validator
     */
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

    /**
     * @param $entity
     */
    public function validate($entity)
    {
        $this->validator->validate($entity);
    }
}

