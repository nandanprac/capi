<?php

namespace ConsultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ConsultBundle\Manager\ValidationError;

/**
 * Questions Controller
 */
class QuestionsController extends Controller
{
    /**
     * @param Request $request - Request Object
     * @return View
     */
    public function postQuestionAction(Request $request)
    {
        $postData = $request->request->get('question');
        $profileToken = $request->headers->get('X-Profile-Token');

        $questionManager = $this->get('consult.question_manager');

        try {
            $question = $questionManager->add((array) json_decode($postData, true), $profileToken);

        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        $files = $request->files;
        $questionImageManager = $this->get('consult.question_image_manager');

        try {
            $questionImageManager->add($question, $files);
        } catch (\Exception $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            array("question" => $question),
            Codes::HTTP_CREATED
        );
    }

    /**
     * @param integer $questionId - Question Id
     * @return \ConsultBundle\Entity\Question|View
     */
    public function getQuestionAction($questionId)
    {
        $questionManager = $this->get('consult.question_manager');

        try {
            $question = $questionManager->load($questionId);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $question) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        } elseif ($question->isSoftDeleted()) {
            return View::create(null, Codes::HTTP_GONE);
        }

        return $question;
    }

    /**
     * @param Request $requestRec - request Object
     * @return array Question - list of question objects
     */
    public function getQuestionsAction(Request $requestRec)
    {
        $questionManager = $this->get('consult.question_manager');
        $request = $requestRec->query->all();

        try {
            $questionList = $questionManager->loadByFilters($request);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $questionList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return array('questions' => $questionList[0], 'count' => $questionList[1]);
    }

    /**
     * returns Question
     */
    public function patchQuestionAction()
    {
        $questionManager = $this->get('consult.question_manager');
        $request = $this->getRequest()->request->all();

        try {
            $questionFinal = $questionManager->patch($request);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            array("question" => $questionFinal),
            Codes::HTTP_CREATED
        );

    }
}
