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

class DoctorQuestionsController extends Controller
{
    /**
     * @param Request $request
     * @return DoctorQuestion
     *
     */
   public function postDoctorQuestionsAction(Request $request)
   {
       $questionId = $request->request->get('questionId');
       $doctorsId = $request->request->get('doctorsId');

       $er=$this->getDoctrine()->getManager()->getRepository("ConsultBundle:Question");
       $question = $er->find($questionId);

       $doctorsQuestion = new DoctorQuestion();
       $doctorsQuestion->setPractoAccountId($doctorsId);
       $doctorsQuestion->setQuestion($question);

       $em = $this->getDoctrine()->getManager();
       $em->persist($doctorsQuestion);
       $em->flush();

       return $doctorsQuestion;
   }

    /**
     * @return ArrayCollection
     *
     */
    public function getDoctorQuestionsAction()
    {
        $request = $this->get('request');
        $queryParams = $request->query->all();
        //TODO Move this in manager
        // practo_account_id is not mandatory
        if (array_key_exists('practo_account_id', $queryParams)) {
            $doctorId = $queryParams['practo_account_id'];
        } else {
            return View::create("Atleast <practo_account_id> is needed.", Codes::HTTP_BAD_REQUEST);
        }

        $doctorQuestionManager = $this->get('consult.doctorQuestionManager');
        list($questions, $count) = $doctorQuestionManager->loadAllByDoctor($doctorId, $queryParams);


        return array("questions"=>$questions, "count"=>$count);
    }

   /**
    *
    * @return mixed
    *
    */
    public function patchDoctorQuestionAction()
    {
        $updateData = $this->getRequest()->request->all();
        $doctorQuestionManager = $this->get('consult.doctorQuestionManager');

        try {
            $doctor_question_mapping_final = $doctorQuestionManager->patch($updateData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(array("question" => $doctor_question_mapping_final), Codes::HTTP_CREATED);
    }

    /**
     * @param $doctorId
     * @return mixed
     *
     */
    public function getAnsweredDoctorQuestionsAction($doctorId)
    {
        $doctorQuestionManager = $this->get('consult.doctorQuestionManager');
        $questions =  $doctorQuestionManager->getAnsweredDoctorQuestionsForDoctor($doctorId);

        return $questions;

    }
}
