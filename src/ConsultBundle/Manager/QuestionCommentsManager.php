<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\QuestionCommentVote;
use ConsultBundle\Entity\QuestionCommentFlag;
use ConsultBundle\Response\QuestionCommentResponse;
use ConsultBundle\Utility\Utility;
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

        if (!array_key_exists('identifier', $requestParams)
            or (array_key_exists('identifier', $requestParams) and empty($requestParams['identifier']))
        ) {
            @$error['identifier'] = 'This cannot be blank';
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
        $identifier = $requestParams['identifier'];
        $initials = $this->generateInitials($identifier);
        $requestParams['identifier'] = $initials;
        $this->updateFields($questionComment, $requestParams);
        $question->addComment($questionComment);
        $question->setModifiedAt(new \DateTime('now'));
        $this->helper->persist($questionComment, true);

        return new QuestionCommentResponse($questionComment);
    }

    /**
     * @param array $requestParams
     *
     * @return array|\ConsultBundle\Entity\QuestionCommentFlag|\ConsultBundle\Entity\QuestionCommentFlag[]
     * @throws \ConsultBundle\Manager\ValidationError
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

        if ($requestParams['practo_account_id'] == $questionComment->getPractoAccountId()) {
            @$error['error'] = 'You cannot vote or flag your own comment!';
            throw new ValidationError($error);
        }

        if (array_key_exists('vote', $requestParams)) {
            $er = $this->helper->getRepository(ConsultConstants::QUESTION_COMMENT_VOTE_ENTITY_NAME);
            $vote = $er->findBy(array('questionComment' => $questionComment, 'practoAccountId' => $requestParams['practo_account_id'], 'softDeleted' => 0));
            if (!empty($vote[0])) {
                $vote = $vote[0];
            }

            if (!empty($vote) and ($vote->getVote() == $requestParams['vote'])) {
                @$error['error'] = 'The user has already voted on this comment';
                throw new ValidationError($error);
            } elseif (!empty($vote) and ($vote->getVote() != $requestParams['vote'])) {
                $vote->setVote($requestParams['vote']);
                $vote->setCount($vote->getCount() + 1);
                $this->helper->persist($vote, true);

               // return $vote;
            } else {
                $commentVote = new QuestionCommentVote();
                $commentVote->setQuestionComment($questionComment);
                $this->updateFields($commentVote, $requestParams);
                $this->helper->persist($commentVote, true);

               // return $commentVote;
            }
        }

        if (array_key_exists('flag', $requestParams) && Utility::toBool($requestParams['flag'])) {
            $er = $this->helper->getRepository(ConsultConstants::QUESTION_COMMENT_FLAG_ENTITY_NAME);
            $flag = $er->findBy(array('questionComment' => $questionComment, 'practoAccountId' => $requestParams['practo_account_id'], 'softDeleted' => 0));
            if (!empty($flag[0])) {
                @$error['error'] = 'The user has already flagged this comment';
                throw new ValidationError($error);
            }

            $flag = new QuestionCommentFlag();
            $flag->setQuestionComment($questionComment);
            $requestParams['flag_code'] = strtoupper($requestParams['flag_code']);
            $requestParams['flag_text'] = (array_key_exists('text', $requestParams)) ? $requestParams['text'] : null ;
            unset($requestParams['flag']);
            if (array_key_exists('text', $requestParams)) {
                unset($requestParams['text']);
            }
            $this->updateFields($flag, $requestParams);
            $this->helper->persist($flag, true);

            //return $flag;
        }

        $er = $this->helper->getRepository(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME);
        return $er->loadComment($requestParams['question_comment_id'], $requestParams['practo_account_id']);

    }


    /**
     * Load Comments By QuestionId
     *
     * @param array $requestParams
     *
     * @return array QuestionComment
     */
    public function loadAll($requestParams)
    {
        if (!array_key_exists('question_id', $requestParams)) {
            @$error['question_id'] = 'This cannot be blank';
            throw new ValidationError($error);
        }
        $limit = (array_key_exists('limit', $requestParams)) ? $requestParams['limit'] : null;
        $offset = (array_key_exists('offset', $requestParams)) ? $requestParams['offset'] : null;
        $practoAccountId = (array_key_exists('practo_account_id', $requestParams)) ? $requestParams['practo_account_id'] : null;

        $question = $this->helper->loadById($requestParams['question_id'], ConsultConstants::QUESTION_ENTITY_NAME);
        if (is_null($question)) {
            @$error['question_id'] = 'Question with this id does not exist';
            throw new ValidationError($error);
        }
        $er = $this->helper->getRepository(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME);
        $questionCommentList = $er->getComments($question, $limit, $offset, $practoAccountId);

        if (empty($questionCommentList)) {
            return null;
        }

        return $questionCommentList;
    }

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
     * @param string $identifier
     *
     * @return string
     */
    private function generateInitials($identifier)
    {
        $list = explode(' ', $identifier);
        if (count($list) >= 2) {
            return strtoupper($list[0][0].$list[1][0]);
        }

        if (ctype_alpha($list[0])) {
            return strtoupper($list[0][0]);
        }

        $temp = explode('@', $identifier);
        $list = preg_split('/[.+_-\d]+/', $temp[0]);

        return ((count($list) >= 2 and !empty($list[0][0]) and !empty($list[1][0])) ? strtoupper($list[0][0].$list[1][0]): ((count($list) > 0 and !empty($list[0][0])) ? strtoupper($list[0][0]): 'ZZ'));
    }
}
