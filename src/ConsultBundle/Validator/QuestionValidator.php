<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 17:26
 */

namespace ConsultBundle\Validator;

use Symfony\Component\Validator\ValidatorInterface;
use ConsultBundle\Entity\BaseEntity;
use ConsultBundle\Manager\ValidationError;

/**
 * Question Validator
 */
class QuestionValidator implements Validator
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

    /**
     * @param BaseEntity $entity - entity to be vaidated
     * @throws ValidationError
     * @return null
     */
    public function validate(BaseEntity $entity)
    {
        $errors = array();
        $validationErrors = $this->validator->validate($entity);
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
                            "created_at", "modified_at", "X-Profile-Token", );
        foreach ($parameters as $parameter) {
            if (array_key_exists($parameter, $requestParams)) {
                unset($requestParams[$parameter]);
            }
        }

        return $requestParams;
    }
}
