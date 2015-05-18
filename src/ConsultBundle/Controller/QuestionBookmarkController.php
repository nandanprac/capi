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
    public function postQuestionBookmarkAction()
    {
        $postData = $this->getRequest()->request->all();
        $questionBookmarkManager = $this->get('consult.question_bookmark_manager');

        try {
            $questionBookmark = $questionBookmarkManager->add($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            array("question_bookmark" => $questionBookmark),
            Codes::HTTP_CREATED);
    }


    public function patchQuestionBookmarkAction()
    {
        $postData = $this->getRequest()->request->all();
        $questionBookmarkManager = $this->get('consult.question_bookmark_manager');

        if (!array_key_exists('delete', $postData)) {
            return View::create("delete key is mandatory to delete the bookmark", Codes::HTTP_BAD_REQUEST);
        }
        if (!array_key_exists('bookmark_id', $postData)) {
            return View::create("bookmark_id is mandatory to delete the bookmark", Codes::HTTP_BAD_REQUEST);
        }

        try {
            $questionBookmark = $questionBookmarkManager->load($postData['bookmark_id']);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }
        //return $questionBookmark;
        try {
            if ($postData['delete'] === 'true') {
                $questionBookmarkManager->remove($questionBookmark);
            }
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            'Bookmark deleted',
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
    public function getQuestionBookmarkAction()
    {
        $questionBookmarkManager = $this->get('consult.question_bookmark_manager');
        $requestData = $this->getRequest()->query->all();
        if (!array_key_exists('question_bookmark_id', $requestData)) {
            return View::create('question_bookmark_id is mandatory', Codes::HTTP_BAD_REQUEST);
        }

        $questionBookmarkId = $requestData['question_bookmark_id'];
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

}
