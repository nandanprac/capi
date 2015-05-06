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
            //For DEMO ONLY Code
           $doctorQuestionManager = $this->get('consult.doctorQuestionManager');
            $doctorQuestionManager->setDoctorsForAQuestions($question);

                //FOR DEMO Only Code End
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        //$router = $this->get('router');
        //$patientGrowthURL = $router->generate('get_patientgrowth', array(
        //    'patientGrowthId' => $patientGrowth->getId()), true);

        return View::create(
            $question,
            Codes::HTTP_CREATED);
    }

    /**
     * Get Question Action
     *
     * @param integer $question
     *
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

    /**
     * Get Question by UserID Action
     *
     * @param integer $practoid
     *
     *
     * @return Array
     *
     */
    public function getQuestionUseridAction($practoId)
    {
        $questionManager = $this->get('consult.question_manager');
        $request = $this->getRequest();
        try {
            $questionList = $questionManager->loadByUserID($practoId);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }
        if (null === $questionList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        } 

        return $questionList;
    }

    public function getQuestionsAction()
    {
        $questionManager = $this->get('consult.question_manager');
        $request = $this->getRequest();
        try {
            $questionList = $questionManager->loadAll();
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }
        if (null === $questionList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return array("questions" => $questionList);
    }
}
