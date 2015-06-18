<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 12:42
 */

namespace ConsultBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use ConsultBundle\Manager\ValidationError;
use Symfony\Component\HttpFoundation\Request;

/**
 * Doctor Question Controller
 */
class DoctorQuestionsController extends BaseConsultController
{

    /**
     * @return ArrayCollection
     *
     */
    public function getDoctorQuestionsAction()
    {
        $request = $this->get('request');
        $queryParams = $request->query->all();
        try {
            $doctorQuestionManager = $this->get('consult.doctorQuestionManager');
            $questionsList = $doctorQuestionManager->loadAllByDoctor($queryParams);
        } catch (\Exception $e) {
            return View::create(json_encode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        if (null === $questionsList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return $questionsList;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function patchDoctorQuestionAction(Request $request)
    {
         $this->authenticate();
        $updateData = $request->request->all();
        $doctorQuestionManager = $this->get('consult.doctorQuestionManager');

        try {
            $doctorQuestionRes = $doctorQuestionManager->patch($updateData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(array("question" => $doctorQuestionRes), Codes::HTTP_CREATED);
    }


    /**
     * @param int $id
     * @Get("/doctor/question/{id}")
     *
     * @return \ConsultBundle\Entity\DoctorQuestion|\FOS\RestBundle\View\View
     */
    public function getDoctorQuestionAction($id)
    {
        $this->authenticate();
        $doctorQuestionManager = $this->get('consult.doctorQuestionManager');
        try {
            //$doctorQuestionManager = $this->get('consult.doctorQuestionManager');
            $question = $doctorQuestionManager->loadById($id);
        } catch (\Exception $e) {
            return View::create(json_encode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        if (null === $question) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return $question;
    }

    /**
     * @param Integer $doctorId
     * @return mixed
     */
    public function getAnsweredDoctorQuestionsAction($doctorId)
    {
        $this->authenticate();
        $doctorQuestionManager = $this->get('consult.doctorQuestionManager');
        $questions =  $doctorQuestionManager->getAnsweredDoctorQuestionsForDoctor($doctorId);

        return $questions;

    }
}
