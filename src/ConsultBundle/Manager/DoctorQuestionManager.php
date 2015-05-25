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
use ConsultBundle\Manager\ValidationError;

class DoctorQuestionManager extends BaseManager
{
    /**
     * @param $questionId
     * @param array $doctorsId
     */
    public function setDoctorsForAQuestions($questionId, Array $doctorsId)
    {
        $question = $this->helper->loadById($questionId, ConsultConstants::$QUESTION_ENTITY_NAME);
        foreach($doctorsId as $doctorId) {
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

    public function patch($updateData) {

        if (array_key_exists('question_id', $updateData) and array_key_exists('practo_account_id', $updateData)) {
            $question = $this->getRepository()->findOneBy(array('practoAccountId'=>$updateData['practo_account_id'], 'question'=>$updateData['question_id']));
            if (!$question) {
                throw new ValidationError(array("error"=>"Question is mapped to this doctor."));
            }
        } else {
            return View::create("<practo_account_id> and <question_id> is required.", Codes::HTTP_BAD_REQUEST);
        }

        if (array_key_exists('rejection_reason', $updateData)) {
            $question->setRejectionReason($updateData['rejection_reason']);
        }

        if (array_key_exists('reject', $updateData) && $updateData['reject'] == 'true') {
            if (!$question->getRejectedAt()) {
                $question->setRejectedAt(new \DateTime());
            } else {
                throw new ValidationError(array("error" => "Question is already rejected by this doctor"));
            }
        }

        if (array_key_exists('view', $updateData) && $updateData['view'] == 'true') {
            if(!$question->getViewedAt()) {
                $question->setViewedAt(new \DateTime());
            } else {
                throw new ValidationError(array("error" => "Question is viewed already by this doctor"));
            }
        }

        $params = $this->validator->validatePatchArguments($updateData);
        $this->updateFields($question, $params);
        $this->helper->persist($question, true);
    }

    public function updateFields($question, $params)
    {
        try {
            $this->validator->validate($question);
        } catch(ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return;
    }

    public function loadAllByDoctor($doctorId, $queryParams = null){

        return $this->getRepository()->findByFilters($doctorId, $queryParams);
    }

    private function getRepository() {

        return $this->helper->getRepository(ConsultConstants::$DOCTOR_QUESTION_ENTITY_NAME);
    }
}
