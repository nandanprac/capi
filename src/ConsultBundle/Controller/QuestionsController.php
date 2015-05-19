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

       // die;
        $questionManager = $this->get('consult.question_manager');
        $userManager = $this->get('consult.user_manager');

       // var_dump($postData);
       // die;

        if (!array_key_exists('practo_account_id', $postData) or !array_key_exists('text', $postData)) {
            return View::create('Missing mandatory paramater - practo_account_id or text', Codes::HTTP_BAD_REQUEST);
        }

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
    public function getQuestionAction()
    {
        $questionManager = $this->get('consult.question_manager');
        $requestData = $this->getRequest()->query->all();
        if (!array_key_exists('question_id', $requestData)) {
            return View::create('question_id is mandatory', Codes::HTTP_BAD_REQUEST);
        }

        $questionId = $requestData['question_id'];
        try {
            $question = $questionManager->load($questionId);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }
        if (null === $question) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

         return $question;
    }

    public function getQuestionsAction(Request $requestRec)
    {
        $questionManager = $this->get('consult.question_manager');
        $request = $requestRec->query->all();
        $questionList = array();
        try {
            if (empty($request))
                $questionList = $questionManager->loadAll();
            else
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

        if (array_key_exists('question_id', $request)) {
            try {
                $question = $questionManager->load($request['question_id']);
            } catch (AccessDeniedException $e) {
                return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
            }

            if (null === $question)
                return View::create(null, Codes::HTTP_NOT_FOUND);
        } else {
            return View::create(null, Codes::HTTP_BAD_REQUEST);
        }

        try {
            $questionFinal = $questionManager->patch($question, $request);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            array("question" => $questionFinal),
            Codes::HTTP_CREATED);

    }
}
