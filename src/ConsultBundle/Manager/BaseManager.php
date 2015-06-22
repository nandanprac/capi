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
use ConsultBundle\Entity\BaseEntity;

/**
 * Class BaseManager
 *
 * @package ConsultBundle\Manager
 */
class BaseManager
{

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
     * @param  Validator $validator
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param  Helper $helper
     */
    public function setHelper(Helper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param BaseEntity $entity
     */
    public function validate($entity)
    {
        $this->validator->validate($entity);
    }
}
