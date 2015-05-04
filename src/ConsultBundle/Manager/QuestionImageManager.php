<?php

namespace ConsultBundle\Manager;



/**
 * Question Image Manager
 */
class QuestionImageManager extends BaseManager
{



    /**
     * Update Fields
     *
     * @param QuestionImage $questionImage  - Question Image
     * @param array         $data           - Array Parameters
     *
     * @return null
     */
    public function updateFields($questionImage, $data)
    {
        $errors = array();
        $questionImage->setAttributes($data);

        $validationErrors = $this->validate($questionImage);

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

        return;
    }
}
