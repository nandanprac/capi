<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 18:26
 */

namespace ConsultBundle\Manager;


use ConsultBundle\Constants\ConsultConstants;
use Doctrine\Common\Collections\ArrayCollection;

class DoctorQuestionManager extends BaseManager{

    /**
     * @param $doctorId
     * @return array
     */
    public function getDoctorQuestionsForDoctor($doctorId){


        $answeredQuestions = $this->getAnsweredDoctorQuestionsForDoctor($doctorId);
        $unAnsweredQuestions = $this->getUnansweredDoctorQuestionsForDoctor($doctorId);
        $rejectedQuestions = $this->getRejectedDoctorQuestionsForDoctor($doctorId);

        $questions= array('AnsweredQuestions'=>$answeredQuestions,
            'UnAnsweredQuestions'=>$unAnsweredQuestions,
            'RejectedQuestions'=>$rejectedQuestions);
        
        return $questions;


    }

    /**
     * @param $doctorId
     * @return array
     */
    public function getUnansweredDoctorQuestionsForDoctor($doctorId){
        return $this->getDoctorQuestionsForStateAndDoctor($doctorId, "UNANSWERED");
    }


    public function getRejectedDoctorQuestionsForDoctor($doctorId){
        return $this->getDoctorQuestionsForStateAndDoctor($doctorId, "REJECTED");

    }

    public function getAnsweredDoctorQuestionsForDoctor($doctorId){
        return $this->getDoctorQuestionsForStateAndDoctor($doctorId, 'ANSWERED');

    }

    private function getDoctorQuestionsForStateAndDoctor($doctorId, $state)
    {
        $er =  $this->getRepository();


        $questions =  $er->findDoctorQuestionsForState($doctorId, $state);

        return $questions;
    }

    private function getRepository()
    {
    return $this->helper->getRepository(ConsultConstants::$DOCTOR_QUESTION_ENTITY_NAME);
    }

}