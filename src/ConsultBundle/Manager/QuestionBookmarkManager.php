<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\ValidatorInterface;
use ConsultBundle\Manager\ValidationError;
use ConsultBundle\Entity\QuestionBookmark;
use Doctrine\Common\Persistence\ObjectRepository;

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
        if (isset($data['question_id'])) {
            unset($data['question_id']);
        }
        $questionBookmark->setAttributes($data);            
        $questionBookmark->setModifiedAt(new \DateTime('now'));

        try {
            $this->validator->validate($questionBookmark);
        } catch(ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return;
    }

    public function delete($questionBookmark)
    {
        $questionBookmark->setSoftDeleted(True);
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
        if (array_key_exists('question_id', $requestparams)) {
            $questionId = $requestparams['question_id'];
        } else {
            throw new ValidationError('question_id is a mandatory parameter');
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

}

