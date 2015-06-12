<?php

namespace ConsultBundle\Validator;

use Symfony\Component\Validator\ValidatorInterface;
use ConsultBundle\Entity\BaseEntity;
use ConsultBundle\Manager\ValidationError;

/**
 * Question Comment Validator
 */
class QuestionCommentValidator implements Validator
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
     * @param BaseEntity $entity - entity to be validated
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
}
