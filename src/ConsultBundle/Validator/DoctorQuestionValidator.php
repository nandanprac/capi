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

<<<<<<< HEAD
/**
 * Doctor Question Validation
 */
class DoctorQuestionValidator implements Validator
{
=======
class DoctorQuestionValidator implements Validator {
>>>>>>> master

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
     * @param BaseEntity $question - Object of Base Entity
     *
     * @return null
     */
    public function validate(BaseEntity $question)
    {
        $errors = array();
        $validationErrors = $this->validator->validate($question);
        if (0 < count($validationErrors)) {
            foreach ($validationErrors as $validationError) {
              $pattern = '/([a-z])([A-Z])/';
              $replace = function ($m) {
                  return $m[1] . '_' . strtolower($m[2]);
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
     * @param Array $requestParams - Query Params of get request
     *
     * @return Array
     */
    public function validatePatchArguments($requestParams)
    {
<<<<<<< HEAD
        $parameters = array("question_id", "_method", "state", "practo_account_id", "created_at", "comment", "X-Profile-Token");
        foreach ($parameters as $parameter) {
            if (array_key_exists($parameter, $requestParams)) {
=======
        $parameters = array("question_id", "_method", "state",
                            "practo_account_id", "created_at", "comment",
                            "X-Profile-Token");
        foreach ($parameters as $parameter)
            if (array_key_exists($parameter, $requestParams))
>>>>>>> master
                unset($requestParams[$parameter]);

        return $requestParams;
    }

}
