<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use FOS\RestBundle\Util\Codes;
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
     * @param $questionBookmark
     * @param $data
     * @throws ValidationError
     */
    public function updateFields($questionBookmark, $data)
    {
        if (array_key_exists('question_id', $data)) {
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

    public function remove($requestParams)
    {
        $error = array();

        if (!array_key_exists('question_id', $requestParams)) {
            @$error['question_id'] = 'This cannot be blank';
            throw new ValidationError($error);
        }

        if (!array_key_exists('practo_account_id', $requestParams)) {
            @$error['practo_account_id'] = 'This cannot be blank';
            throw new ValidationError($error);
        }

        $er =  $this->helper->getRepository(ConsultConstants::$QUESTION_BOOKMARK_ENTITY_NAME);
        $questionBookmark = $er->findBookmark($requestParams['practo_account_id'], $requestParams['question_id']);

        if (empty($questionBookmark)) {
            @$error['error']='Bookmark doesnt exist';
            throw new ValidationError($error);
        } else {
            $this->helper->remove($questionBookmark[0]);
        }
    }

    /**
     * @param $requestParams
     * @return QuestionBookmark
     * @throws ValidationError
     */
    public function add($requestParams)
    {
        $error = array();
        if (!array_key_exists('question_id', $requestParams)) {
            @$error['question_id'] = 'This cannot be blank';
            throw new ValidationError($error);
        }

        if (!array_key_exists('practo_account_id', $requestParams)) {
            @$error['practo_account_id'] = 'This cannot be blank';
            throw new ValidationError($error);
        }

        $questionId = $requestParams['question_id'];
        $question = $this->helper->loadById($questionId, ConsultConstants::$QUESTION_ENTITY_NAME);

        if (empty($question)) {
            @$error['question_id'] = 'Question with this id doesnt exist';
            throw new ValidationError($error);
        }

        if($question->getPractoAccountId() == $requestParams['practo_account_id']) {
            @$error['error'] = 'User cannot bookmark the question';
            throw new ValidationError($error);
        }

        if ($this->validator->checkUniqueness($question, $requestParams['practo_account_id'])) {
            @$error['error'] = 'This user has already bookmarked this question';
            throw new ValidationError($error);
        }


        $questionBookmark = new QuestionBookmark();
        $questionBookmark->setQuestion($question);
        $this->updateFields($questionBookmark, $requestParams);
        $question->addBookmark($questionBookmark);
        $question->setModifiedAt(new \DateTime('now'));
        $this->helper->persist($questionBookmark, true);

        return $questionBookmark;
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

