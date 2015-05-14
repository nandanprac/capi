<?php

namespace ConsultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ConsultBundle\Manager\ValidationError;

/**
 * Questions Controller
 *
 */
class QuestionsController extends Controller
{
    /**
     * Create Question
     *
     * @return View
     */
    public function postQuestionAction()
    {
        $postData = $this->getRequest()->request->all();
        $questionManager = $this->get('consult.question_manager');

        try {
            $question = $questionManager->add($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            $question,
            Codes::HTTP_CREATED);
    }

    /**
     * Get Question Action
     *
     * @param integer $question
     *
     * @return Array
     *
     */
    public function getQuestionAction()
    {
        $questionManager = $this->get('consult.question_manager');
        $requestData = $this->getRequest()->query->all();
        if (!array_key_exists('question_id', $requestData)) {
            return View::create(null, Codes::HTTP_BAD_REQUEST);
        }

        $questionId = $requestData['question_id'];
        try {
            $question = $questionManager->load($questionId);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }
        if (null === $question) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        } else if ($question->isSoftDeleted()) {
            return View::create(null, Codes::HTTP_GONE);
        }

        return $question;
    }

    public function getQuestionsAction()
    {
        $questionManager = $this->get('consult.question_manager');
        $request = $this->getRequest()->query->all();
        try {
            if (empty($request)) {
                $questionList = $questionManager->loadAll();
            } else {
                if (array_key_exists('practo_account_id', $request) and !array_key_exists('bookmarks', $request)) {
                    $questionList = $questionManager->loadByAccId($request['practo_account_id']);
                }
                if (array_key_exists('bookmarks', $request) and $request['bookmarks'] === 'true' and array_key_exists('practo_account_id', $request)) {
                    $bookmarkList = $questionManager->loadBookmarksById($request['practo_account_id']);
                }
                if (array_key_exists('modified_at', $request)) {
                    $questionList = $questionManager->loadByModifiedTime(new \DateTime('now'));
                }
                if (array_key_exists('state', $request)) {
                    $limit = 100;
                    $offset = 0;
                    if (array_key_exists('limit', $request)) {
                        $limit = $request['limit'];
                    }
                    if (array_key_exists('offset', $request)) {
                        $offset = $request['offset'];
                    }
                    $questionList = $questionManager->loadFeed($request['state'], $limit, $offset);
                }
            }
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

/*        if (null === $questionList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }
*/
        if (!empty($questionList)) {
            return $questionList;
        }
        if (!empty($bookmarkList)) {
            return $bookmarkList;
        }
        return View::create(null, Codes::HTTP_NOT_FOUND);
    }

    public function patchQuestionAction()
    {
        $questionManager = $this->get('consult.question_manager');
        $request = $this->getRequest()->request->all();

        if (array_key_exists('question_id', $request)) {
            try {
                $question = $questionManager->load($request['question_id']);
            } catch (AccessDeniedException $e) {
                return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
            }
            if (null === $question) {
                return View::create(null, Codes::HTTP_NOT_FOUND);
            } else if ($question->isSoftDeleted()) {
                return View::create(null, Codes::HTTP_GONE);
            }
        } else {
            return View::create(null, Codes::HTTP_BAD_REQUEST);
        }

        try {
            $questionFinal = $questionManager->patch($question, $request);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        return $questionList;
        return View::create(
            $questionFinal,
            Codes::HTTP_CREATED);
    }
}
