<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 12:42
 */

namespace ConsultBundle\Controller;

use ConsultBundle\Entity\DoctorQuestion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Persistence\ObjectRepository;
use ConsultBundle\Manager\ValidationError;
use Symfony\Component\HttpFoundation\Request;

/**
 * Doctor Question Controller
 */
class DoctorQuestionsController extends Controller
{

    /**
     * @return ArrayCollection
     */
    public function getDoctorQuestionsAction()
    {
        $request = $this->get('request');
        $queryParams = $request->query->all();
		try{
        	$doctorQuestionManager = $this->get('consult.doctorQuestionManager');
        	$questionsList = $doctorQuestionManager->loadAllByDoctor($queryParams);
		} catch (\Exception $e) {
			return View::create(json_encode($e->getMessage(), true), Codes::HTTP_INTERNAL_SERVER_ERROR);
		}
        return $questionsList;
    }

	/**
    *
    * @return mixed
    */
    public function patchDoctorQuestionAction()
    {
        $updateData = $this->getRequest()->request->all();
        $doctorQuestionManager = $this->get('consult.doctorQuestionManager');

        try {
            $doctorQuestionMapping = $doctorQuestionManager->patch($updateData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(array("question" => $doctorQuestionMapping), Codes::HTTP_CREATED);
    }

    /**
     * @param Integer $doctorId
     * @return mixed
     */
    public function getAnsweredDoctorQuestionsAction($doctorId)
    {
        $doctorQuestionManager = $this->get('consult.doctorQuestionManager');
        $questions =  $doctorQuestionManager->getAnsweredDoctorQuestionsForDoctor($doctorId);

        return $questions;

    }
}
