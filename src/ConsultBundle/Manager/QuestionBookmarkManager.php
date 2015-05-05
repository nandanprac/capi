<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\ValidatorInterface;
use ConsultBundle\Manager\ValidationError;
use ConsultBundle\Entity\QuestionBookmark;

/**
 * Question Bookmark Manager
 */
class QuestionBookmarkManager extends BaseManager
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

    public function createBookmarkForAQuestion($practoAccountId, $questionId)
    {
         $questionBookmark = new QuestionBookmark();
        $questionBookmark->setPractoAccountId($practoAccountId);

        $question = $this->helper->loadById($questionId, ConsultConstants::$QUESTION_ENTITY_NAME);

        $questionBookmark->setQuestion($question);

        $this->helper->persist($questionBookmark, true);
    }
}
