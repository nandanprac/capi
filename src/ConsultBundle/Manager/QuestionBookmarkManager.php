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
        if (isset($data['bookmark'])) {
            unset($data['bookmark']);
        }
        if (isset($data['questionId'])) {
            unset($data['questionId']);
        }
        $questionBookmark->setAttributes($data);            

        $validationErrors = $this->validator->validate($questionBookmark);

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

    /**
     * Add a new bookmark entry 
     *
     * @param $array requestParams 
     *
     * @return null
     */
    public function add($requestparams)
    {
        if (array_key_exists('questionId', $requestparams)) {
            $questionId = $requestparams['questionId'];
        } else {
            throw new ValidationError('QuestionID is a mandatory parameter');
        }

        $question = $this->helper->loadById($questionId, ConsultConstants::$QUESTION_ENTITY_NAME);

        $questionBookmark = new QuestionBookmark();
        $questionBookmark->setQuestion($question);
        $this->updateFields($questionBookmark, $requestparams);
        $question->addBookmark($questionBookmark);

        $this->helper->persist($questionBookmark, true);
    }

    /**
     * Load Bookmark By Id
     *
     * @param integer $questionBookmarkId
     *
     * @return QuestionBookmark
     */
    public function load($questionBookmarkId)
    {

        $questionBookmark = $this->helper->loadById($questionBookmarkId, ConsultConstants::$QUESTION_BOOKMARK_ENTITY_NAME);


        if (is_null($questionBookmark)) {
            return null;
        }

        return $questionBookmark;
    }


    /**
     * Return all bookmarks for a user
     *
     * @param interger      $practoId
     *
     * @return array Question
     */
    public function loadByUserID($practoId)
    {
        $questionList = $this->helper->getRepository(
                                    ConsultConstants::$QUESTION_BOOKMARK_ENTITY_NAME)->findBy(
                                                                        array('practoAccountId' => $practoId,
                                                                              'softDeleted' => 0));
        if (is_null($questionList)) {
            return null;
        }

        return $questionList;
    }

}

