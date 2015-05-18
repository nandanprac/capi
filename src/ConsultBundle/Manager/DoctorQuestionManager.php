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

class DoctorQuestionManager extends BaseManager
{
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


    public function loadAll($doctorId, $queryParams = null){
        $result = $this->getRepository()->findByFiltersDoctorQuestions($doctorId, $queryParams);
        var_dump($result);
        die;
    }

    private function getRepository()
    {
        return $this->helper->getRepository(ConsultConstants::$DOCTOR_QUESTION_ENTITY_NAME);
    }

}
