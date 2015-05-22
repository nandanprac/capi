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
 *
 */
class QuestionsController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function postQuestionAction(Request $request)
    {
        $postData = $request->request->all();
        $questionManager = $this->get('consult.question_manager');

        try {
            $question = $questionManager->add($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            var_dump($e->getCode() + $e->getMessage() + $e->getTraceAsString());
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        $files = $request->files;
        $questionImageManager = $this->get('consult.question_image_manager');

        try{
           $questionImageManager->add($question, $files);
        }catch(\Exception $e)
        {
            var_dump($e->getCode() + $e->getMessage() + $e->getTraceAsString());
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            array("question" => $question) ,
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
    public function getQuestionAction($questionId)
    {
        $questionManager = $this->get('consult.question_manager');
        $request = $this->getRequest();
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

    public function getQuestionsAction(Request $requestRec)
    {
        $questionManager = $this->get('consult.question_manager');
        $request = $requestRec->query->all();
        $questionList = array();
        try {
            $questionList = $questionManager->loadByFilters($request);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $questionList)
            return View::create(null, Codes::HTTP_NOT_FOUND);
        return array('questions' => $questionList[0], 'count' => $questionList[1]);
    }

    public function patchQuestionAction()
    {
        $questionManager = $this->get('consult.question_manager');
        $request = $this->getRequest()->request->all();

        try {
            $questionFinal = $questionManager->patch($request);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        } 

        return View::create(
            array("question" => $questionFinal),
            Codes::HTTP_CREATED);

    }
}
