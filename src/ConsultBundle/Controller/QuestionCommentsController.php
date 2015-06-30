<?php

namespace ConsultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ConsultBundle\Manager\ValidationError;

/**
 * Question Comments Controller
 */
class QuestionCommentsController extends BaseConsultController
{
    /**
     * @param Request $request
     * @return View
     */
    public function postQuestionCommentAction(Request $request)
    {
        $this->authenticate();
        $postData = $request->request->all();
        $questionCommentsManager = $this->get('consult.question_comment_manager');

        try {
            $questionComment = $questionCommentsManager->add($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            $questionComment,
            Codes::HTTP_CREATED
        );
    }

    /**
     * Patch Question Comment Action
     *
     * @param Request $request - the Request object
     * @return View
     */
    public function patchQuestionCommentAction(Request $request)
    {
        $this->authenticate();
        $postData = $request->request->all();
        $questionCommentsManager = $this->get('consult.question_comment_manager');

        try {
            $questionComment = $questionCommentsManager->patch($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            $questionComment,
            Codes::HTTP_CREATED
        );
    }

    /**
     * Get Question Comment Action
     *
     * @param Request $request - the Request object
     *
     * @return array QuestionComment
     */
    public function getQuestionCommentAction(Request $request)
    {
        $postData = $request->query->all();
        $questionCommentsManager = $this->get('consult.question_comment_manager');

        try {
            $questionCommentList = $questionCommentsManager->loadAll($postData);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }
        if (empty($questionCommentList)) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return $questionCommentList;
    }
}
