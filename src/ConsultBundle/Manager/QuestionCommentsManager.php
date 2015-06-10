<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\QuestionCommentVote;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\ValidatorInterface;
use ConsultBundle\Manager\ValidationError;
use ConsultBundle\Entity\QuestionComment;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Question Bookmark Manager
 */
class QuestionCommentsManager extends BaseManager
{


    /**
     * @param QuestionComment $questionComment - comment object
     * @param array            $data           - data to be updated
     * @throws ValidationError
     */
    private function updateFields($entity, $data)
    {
        if (array_key_exists('question_id', $data)) {
            unset($data['question_id']);
        }
        if (array_key_exists('question_comment_id', $data)) {
            unset($data['question_comment_id']);
        }
        $entity->setAttributes($data);

        try {
            $this->validator->validate($entity);
        } catch (ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return;
    }

    /**
     * @param array $requestParams - params for comment addition
     * @return QuestionComment
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
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);

        if (empty($question)) {
            @$error['question_id'] = 'Question with this id doesnt exist';
            throw new ValidationError($error);
        }


        $questionComment = new QuestionComment();
        $questionComment->setQuestion($question);
        $this->updateFields($questionComment, $requestParams);
        $question->addComment($questionComment);
        $question->setModifiedAt(new \DateTime('now'));
        $this->helper->persist($questionComment, true);

        return $questionComment;
    }

    /**
     * Patch comment for upvote/downvote
     *
     * @param array $requestParams
     *
     * @return QuestionComment
     */
    public function patch($requestParams)
    {
        if (!array_key_exists('question_comment_id', $requestParams)) {
            @$error['question_comment_id'] = 'This value cannot be blank';
            throw new ValidationError($error);
        }
        if (!array_key_exists('practo_account_id', $requestParams)) {
            @$error['practo_account_id'] = 'This value cannot be blank';
            throw new ValidationError($error);
        }

        $questionComment = $this->helper->loadById($requestParams['question_comment_id'], ConsultConstants::QUESTION_COMMENT_ENTITY_NAME);
        if (empty($questionComment)) {
            @$error['question_comment_id'] = 'Comment with this id doesnt exist';
            throw new ValidationError($error);
        }

        $er = $this->helper->getRepository(ConsultConstants::QUESTION_COMMENT_VOTE_ENTITY_NAME);
        $hasVoted = $er->findBy(array('questionComment' => $questionComment, 'practoAccountId' => $requestParams['practo_account_id']));
        if (!empty($hasVoted)) {
            @$error['error'] = 'This user cannot re-vote on this comment';
            throw new ValidationError($error);
        }

        $commentVote = new QuestionCommentVote();
        $commentVote->setQuestionComment($questionComment);
        $this->updateFields($commentVote, $requestParams);
        $this->helper->persist($commentVote, true);

        return $commentVote;

    }


    /**
     * Load Comments By QuestionId
     *
     * @param Request $request - Request object
     *
     * @return array QuestionComment
     */
    public function loadAll($requestParams)
    {
        if (!array_key_exists('question_id', $requestParams)) {
            @$error['question_id'] = 'This cannot be blank';
            throw new ValidationError($error);
        }
        $limit = (array_key_exists('limit', $requestParams)) ? $requestParams['limit'] : 10;
        $offset = (array_key_exists('offset', $requestParams)) ? $requestParams['offset'] : 0;

        $question = $this->helper->loadById($requestParams['question_id'], ConsultConstants::QUESTION_ENTITY_NAME);
        if(is_null($question)) {
            @$error['question_id'] = 'Question with this id does not exist';
            throw new ValidationError($error);
        }
        $er = $this->helper->getRepository(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME);
        $questionCommentList = $er->getComments($question, $limit, $offset);

        if (empty($questionCommentList)) {
            return null;
        }

        return $questionCommentList;
    }
}
