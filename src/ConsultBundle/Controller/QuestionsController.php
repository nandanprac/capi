<?php

namespace ConsultBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ConsultBundle\Manager\ValidationError;

/**
 * Questions Controller
 */
class QuestionsController extends BaseConsultController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \FOS\RestBundle\View\View
     * @throws \HttpException
     */
    public function postQuestionAction(Request $request)
    {
        $logger = $this->get('logger');
        $logger->info("Post Question".$request);
        $practoAccountId = $this->authenticate();
        $this->checkPatientConsent($practoAccountId, true);

        $postData = $request->request->get('question');
        if (empty($postData)) {
            return View::create("Key question not found", Codes::HTTP_BAD_REQUEST);
        }
        if (!is_array($postData)) {
            $postData = json_decode($postData, true);
        }
        //$practoAccountId = $request->request->get('practo_account_id');
        $profileToken = $request->headers->get('X-Profile-Token');

        $questionManager = $this->get('consult.question_manager');

        try {
            $question = $questionManager->add($postData, $practoAccountId, $profileToken);

        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        $files = $request->files;
        $questionImageManager = $this->get('consult.question_image_manager');

        try {
            $questionImageManager->add($question->getId(), $files);
        } catch (HttpException $he) {
            return View::create($he->getMessage(), $he->getStatusCode());
        } catch (\Exception $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            array("question" => $question),
            Codes::HTTP_CREATED
        );
    }

    /**
     * @param int                                       $questionId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \ConsultBundle\Entity\Question|\FOS\RestBundle\View\View
     */
    public function getQuestionAction($questionId, Request $request)
    {
        $logger = $this->get('logger');
        $logger->info("Get Question".$questionId);
        $practoAccountId = $this->authenticate(false);

        $questionManager = $this->get('consult.question_manager');

        try {
            $question = $questionManager->load($questionId, $practoAccountId);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $question) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
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

        return $questionList;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function patchQuestionAction(Request $request)
    {
        $practoAccountId = $this->authenticate(false);
        $this->checkPatientConsent($practoAccountId, true);

        $questionManager = $this->get('consult.question_manager');
        $request = $request->request->all();

        try {
            $questionFinal = $questionManager->patch($request, $practoAccountId);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        } catch (\HttpException $e) {
            return View::create(json_decode($e->getMessage(), true), $e->getCode());
        }

        return View::create(
            array("question" => $questionFinal),
            Codes::HTTP_CREATED
        );

    }

    /**
     * For dev use only
     * @param int $id
     */
    public function deleteQuestionAction($id)
    {
        $practoAccountId = $this->authenticate();

        if ($practoAccountId != 123412) {
            throw new HttpException(Codes::HTTP_FORBIDDEN, "Unauthorised Access");
        }

        $questionManager = $this->get('consult.question_manager');
        $questionManager->delete($id);
    }
}
