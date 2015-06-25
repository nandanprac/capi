<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 17:26
 */

namespace ConsultBundle\Validator;

use Symfony\Component\Validator\ValidatorInterface;

/**
 * Question Validator
 */
class QuestionValidator extends BaseValidator
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
     * @param array $requestParams - parameters that cannot be changed
     * @return array
     */
    public function validatePatchArguments($requestParams)
    {
        $parameters = array("view", "share", "question_id", "_method", "state",
                            "practo_account_id", "created_at", "comment",
                            "c_text", "X-Profile-Token", );
        foreach ($parameters as $parameter) {
            if (array_key_exists($parameter, $requestParams)) {
                unset($requestParams[$parameter]);
            }
        }

        return $requestParams;
    }

    /**
     * @param array $requestParams - parameters that cannot be changed
     * @return array
     */
    public function validatePostArguments($requestParams)
    {
        $parameters = array("view", "share", "question_id", "state",
                            "created_at", "modified_at", );
        foreach ($parameters as $parameter) {
            if (array_key_exists($parameter, $requestParams)) {
                unset($requestParams[$parameter]);
            }
        }

        return $requestParams;
    }
}
