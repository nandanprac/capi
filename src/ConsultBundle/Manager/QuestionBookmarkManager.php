<?php

namespace ConsultBundle\Manager;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\ValidatorInterface;
use ConsultBundle\Manager\ValidationError;

/**
 * Question Bookmark Manager
 */
class QuestionBookmarkManager
{


    /**
     * Update Fields
     *
     * @param QuestionBookmark $questionBookmark  - Question Bookmark
     * @param array            $data              - Array Parameters
     *
     * @return null
     */
    public function updateFields($questionBookmark, $data)
    {
        $errors = array();
        if ($data['bookmark']) {
            unset($data['bookmark']);
            $questionBookmark->setAttributes($data);            
        }

        $validationErrors = $this->validate($questionBookmark);

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
