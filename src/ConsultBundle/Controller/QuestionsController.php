<?php

namespace ConsultBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ConsultBundle\Manager\ValidationError;

/**
 * Questions Controller
 *
 */
class QuestionsController extends BaseConsultController
{
    /**
<<<<<<< HEAD
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \FOS\RestBundle\View\View
     * @throws \HttpException
=======
     * @param Request $request
     * @return View
>>>>>>> master
     */
    public function postQuestionAction(Request $request)
    {
        $logger = $this->get('logger');
        $logger->info("Post Question".$request);
        $this->authenticate();
        $postData = $request->request->get('question');
        $practoAccountId = $request->request->get('practo_account_id');
        $profileToken = $request->headers->get('X-Profile-Token');
        //var_dump($postData);
        //var_dump(json_decode($postData));die;
       // $question = $post
        $questionManager = $this->get('consult.question_manager');

        try {
<<<<<<< HEAD
            $question = $questionManager->add((array) json_decode($postData, true), $practoAccountId, $profileToken);

        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
=======

            $question = $questionManager->add((array)json_decode($postData, true), $profileToken);

        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
>>>>>>> master
        }

        $files = $request->files;
//        var_dump($files);die;
        $questionImageManager = $this->get('consult.question_image_manager');

        try {
<<<<<<< HEAD
            $questionImageManager->add($question->getId(), $files);
        } catch (\Exception $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
=======
           $questionImageManager->add($question, $files);
        } catch(\Exception $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
>>>>>>> master
        }

        return View::create(
            array("question" => $question),
            Codes::HTTP_CREATED);
    }

    /**
<<<<<<< HEAD
     * @param int                                       $questionId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \ConsultBundle\Entity\Question|\FOS\RestBundle\View\View
=======
     * Get Question Action
     *
     * @param integer $question
     *
     * @return Array
     *
>>>>>>> master
     */
    public function getQuestionAction($questionId, Request $request)
    {
        $logger = $this->get('logger');
        $logger->info("Get Question".$questionId);
        $practoAccountId = $this->authenticate(false);

        $questionManager = $this->get('consult.question_manager');
        $request = $this->getRequest();

        try {
            $question = $questionManager->load($questionId, $practoAccountId);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $question) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
<<<<<<< HEAD
=======
        } else if ($question->isSoftDeleted()) {
            return View::create(null, Codes::HTTP_GONE);
>>>>>>> master
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

        return $questionList;
    }

<<<<<<< HEAD
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function patchQuestionAction(Request $request)
=======
    public function patchQuestionAction()
>>>>>>> master
    {
        $practoAccountId = $this->authenticate(false);
        $questionManager = $this->get('consult.question_manager');
        $request = $request->request->all();

        try {
            $questionFinal = $questionManager->patch($request, $practoAccountId);
        } catch (ValidationError $e) {
<<<<<<< HEAD
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        } catch(\HttpException $e) {
            return View::create(json_decode($e->getMessage(), true), $e->getCode());
        }
=======
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        } 
>>>>>>> master

        return View::create(
            array("question" => $questionFinal),
            Codes::HTTP_CREATED);

    }
}
