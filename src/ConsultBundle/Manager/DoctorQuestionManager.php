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
use ConsultBundle\Utility\RetrieveDoctorProfileUtil;
use ConsultBundle\Utility\RetrieveUserProfileUtil;

class DoctorQuestionManager extends BaseManager
{
    /**
     * @param RetrieveUserProfileUtil   $retrieveUserProfileUtil
     * @param RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil
     */
    public function __construct(RetrieveUserProfileUtil $retrieveUserProfileUtil, RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil )
    {
        $this->retrieveUserProfileUtil = $retrieveUserProfileUtil;
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
    }

    /**
     * @param $questionId
     * @param array      $doctorsId
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

    /**
     * @param $updateData
     * @return \ConsultBundle\Entity\Question
     * @throws ValidationError
     */
    public function patch($updateData) 
    {

        if (array_key_exists('question_id', $updateData) and array_key_exists('practo_account_id', $updateData)) {
            /**
             * @var DoctorQuestion $question
             */
            $question = $this->getRepository()->findOneBy(array('practoAccountId'=>$updateData['practo_account_id'], 'question'=>$updateData['question_id']));
            if (!$question) {
                throw new ValidationError(array("error"=>"Question is not mapped to this doctor."));
            }
        } else {
            throw new ValidationError(array("error"=> "practo_account_id and question_id is required."));
        }


        if (array_key_exists('reject', $updateData) && $updateData['reject'] === true) {
            if (array_key_exists('rejection_reason', $updateData)) {
                $question->setRejectionReason($updateData['rejection_reason']);
            }
            if (!$question->getRejectedAt()) {
                $question->setRejectedAt(new \DateTime());
                $question->setViewedAt(new \DateTime());
                $question->setState("REJECTED");
            } else {
                throw new ValidationError(array("error" => "Question is already rejected by this doctor"));
            }
        } else if (array_key_exists('reject', $updateData) && $updateData['reject'] === false and array_key_exists('rejection_reason', $updateData)) {
            throw new ValidationError(array("error"=> "Please dont pass rejection_reason if reject is false"));
        }

        if (array_key_exists('view', $updateData) && $updateData['view'] == 'true') {
            if(!$question->getViewedAt()) {
                $question->setViewedAt(new \DateTime());
            }
        }

        $params = $this->validator->validatePatchArguments($updateData);
        $this->updateFields($question, $params);
        $this->helper->persist($question, true);


        $ques = $question->getQuestion();

        $this->retrieveUserProfileUtil->loadUserDetailInQuestion($ques);
        $this->retrieveDoctorProfileUtil->retrieveDoctorProfileForQuestion($ques);

        return $ques;
    }

    /**
     * @param $question
     * @param $params
     * @throws ValidationError
     */
    private function updateFields($question, $params)
    {
        try {
            $this->validator->validate($question);
        } catch(ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return;
    }

    public function loadById($doctorQuestionId)
    {

        return $this->getRepository()->findById($doctorQuestionId);
    }

    /**
     * @param $doctorId
     * @param null     $queryParams
     * @return mixed
     */
    public function loadAllByDoctor($doctorId, $queryParams = null)
    {
        return $this->getRepository()->findByFilters($doctorId, $queryParams);
    }

    private function getRepository() 
    {

        return $this->helper->getRepository(ConsultConstants::$DOCTOR_QUESTION_ENTITY_NAME);
    }
}
