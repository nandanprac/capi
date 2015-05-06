<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 18:26
 */

namespace ConsultBundle\Manager;


use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\DoctorQuestion;
use Doctrine\Common\Collections\ArrayCollection;

class DoctorQuestionManager extends BaseManager{

    /**
     * @param $doctorId
     * @return array
     */
    public function getDoctorQuestionsForDoctor($doctorId){


        /*$answeredQuestions = $this->getAnsweredDoctorQuestionsForDoctor($doctorId);
        $unAnsweredQuestions = $this->getUnansweredDoctorQuestionsForDoctor($doctorId);
        $rejectedQuestions = $this->getRejectedDoctorQuestionsForDoctor($doctorId);

        $questions= array('AnsweredQuestions'=>$answeredQuestions,
            'UnAnsweredQuestions'=>$unAnsweredQuestions,
            'RejectedQuestions'=>$rejectedQuestions);*/

        $questions = $this->getDoctorQuestionsForStateAndDoctor($doctorId);

        return $questions;


    }

    /**
     * @param $questionId
     * @param array $doctorsId
     */
    public function setDoctorsForAQuestions($questionId, Array $doctorsId)
    {
        $question = $this->helper->loadById(ConsultConstants::$QUESTION_ENTITY_NAME, $questionId);

        foreach($doctorsId as $doctorId)
        {
            $this->createDoctorQuestionEntity($question, $doctorId);
        }

        $this->helper->persist(null, true);
    }

    private function createDoctorQuestionEntity($question, $doctorId )
    {
        $doctorQuestion = new DoctorQuestion();
        $doctorQuestion->setQuestion($question);
        $doctorQuestion->setPractoAccountId($doctorId);
        $this->helper->persist($doctorQuestion);
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

    /**
     * @param $doctorId
     * @param null $state
     * @return mixed
     */
    private function getDoctorQuestionsForStateAndDoctor($doctorId, $state=null)
    {
        $er =  $this->getRepository();


        $questions =  $er->findDoctorQuestionsForAState($doctorId, $state);

        return $questions;
    }

    private function getRepository()
    {
    return $this->helper->getRepository(ConsultConstants::$DOCTOR_QUESTION_ENTITY_NAME);
    }

}