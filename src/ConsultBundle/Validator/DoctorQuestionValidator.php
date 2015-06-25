<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 18:25
 */

namespace ConsultBundle\Validator;

use Symfony\Component\Validator\ValidatorInterface;
use ConsultBundle\Entity\BaseEntity;
use ConsultBundle\Manager\ValidationError;

/**
 * Doctor Question Validation
 */
class DoctorQuestionValidator extends BaseValidator
{
    /**
     * Constructor
     *
     * @param ValidatorInterface $validator - Validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct($validator);
    }

    /**
     * @param Array $requestParams - Query Params of get request
     *
     * @return Array
     */
    public function validatePatchArguments($requestParams)
    {
        $parameters = array("question_id", "_method", "state", "practo_account_id", "created_at", "comment", "X-Profile-Token");
        foreach ($parameters as $parameter) {
            if (array_key_exists($parameter, $requestParams)) {
                unset($requestParams[$parameter]);
            }
        }

        return $requestParams;
    }
}
