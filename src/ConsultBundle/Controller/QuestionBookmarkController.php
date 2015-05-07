<?php

namespace ConsultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ConsultBundle\Manager\ValidationError;

/**
 * Questions Controller
 *
 */
class QuestionBookmarkController extends Controller
{
    /**
     * Create Question bookmark
     *
     * @return View
     */
    public function postQuestionbookmarkAction()
    {
        $postData = $this->getRequest()->request->all();
        $questionBookmarkManager = $this->get('consult.question_bookmark_manager');

        try {
            $questionBookmark = $questionBookmarkManager->add($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            $questionBookmark,
            Codes::HTTP_CREATED);
    }

    /**
     * Get Question Bookmark Action
     *
     * @param integer $questionBookmarkId
     *
     * @return Array
     *
     */
    public function getQuestionbookmarkAction($questionBookmarkId)
    {
        $questionBookmarkManager = $this->get('consult.question_bookmark_manager');
        try {
            $questionBookmark = $questionBookmarkManager->load($questionBookmarkId);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }
        if (null === $questionBookmark) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        } else if ($questionBookmark->isSoftDeleted()) {
            return View::create(null, Codes::HTTP_GONE);
        }

        return $questionBookmark;
    }

    /**
     * Get Question by UserID Action
     *
     * @param integer $practoid
     *
     *
     * @return Array
     *
     */
    public function getQuestionbookmarkUseridAction($practoId)
    {
        $questionBookmarkManager = $this->get('consult.question_bookmark_manager');
        try {
            $questionBookmarkList = $questionBookmarkManager->loadByUserID($practoId);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }
        if (null === $questionBookmarkList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        } 

        return $questionBookmarkList;
    }
}