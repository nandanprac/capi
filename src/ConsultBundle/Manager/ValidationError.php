<?php

namespace ConsultBundle\Manager;

use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Validation Error
 */
class ValidationError extends \ErrorException
{
    protected $errorFields;

    /**
     * Constructor
     *
     * @param ConstraintViolationList|array|string $errors
     */
    public function __construct($errors)
    {
        if ($errors instanceof ConstraintViolationList) {
            $errorFields = array();
            foreach ($errors as $error) {
                $attrSnake = $this->convertToSnakeCase($error->getPropertyPath());
                $errorFields[$attrSnake][] = $error->getMessage();
            }
            $errorMessage = json_encode($errorFields);
        } else if (is_array($errors)) {
            $errorFields = $errors;
            $errorMessage = json_encode($errorFields);
        } else {
            $errorFields = $errors;
            $errorMessage = $errors;
        }
        parent::__construct($errorMessage);
        $this->setErrorFields($errorFields);
    }

    /**
     * Set Error Fields
     *
     * @param array|string $errorFields
     */
    public function setErrorFields($errorFields)
    {
        $this->errorFields = $errorFields;
    }

    /**
     * Get Error Fields
     *
     * @return array|string
     */
    public function getErrorFields()
    {
        return $this->errorFields;
    }

    /**
     * Convert To Snake Case
     *
     * @param string $attrCamel - Camel Cased Attribute
     *
     * @return string
     */
    protected function convertToSnakeCase($attrCamel)
    {
        $pattern = '/([a-z])([A-Z])/';
        $replace = function ($m) {
            return $m[1] . '_' . strtolower($m[2]);
        };

        return preg_replace_callback($pattern, $replace, $attrCamel);
    }
}