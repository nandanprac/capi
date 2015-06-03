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

class DoctorQuestionValidator implements Validator
{

    private $validator;

    /**
     * Constructor
     *
     * @param ValidatorInterface $validator - Validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }


    public function validate(BaseEntity $question)
    {
        $errors = array();
        $validationErrors = $this->validator->validate($question);
        if (0 < count($validationErrors)) {
            foreach ($validationErrors as $validationError) {
                $pattern = '/([a-z])([A-Z])/';
                $replace = function ($m) {
                    return $m[1].'_'.strtolower($m[2]);
                };
                $attribute = preg_replace_callback($pattern, $replace, $validationError->getPropertyPath());
                @$errors[$attribute][] = $validationError->getMessage();
            }
        }

        if (0 < count($errors)) {
            throw new ValidationError($errors);
        }
    }

    public function validatePatchArguments($requestParams)
    {
        $parameters = array("question_id", "_method", "state",
                            "practo_account_id", "created_at", "comment",
                            "X-Profile-Token");
        foreach ($parameters as $parameter) {
            if (array_key_exists($parameter, $requestParams)) {
                unset($requestParams[$parameter]);
            }
        }

        return $requestParams;
    }
}
