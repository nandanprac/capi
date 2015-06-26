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
use ConsultBundle\Repository\DoctorQuestionRepository;
use ConsultBundle\Repository\QuestionCommentRepository;
use ConsultBundle\Response\DoctorQuestionResponseObject;
use ConsultBundle\Response\ReplyResponseObject;
use ConsultBundle\Utility\Utility;
use Doctrine\Common\Collections\ArrayCollection;
use ConsultBundle\Manager\ValidationError;
use ConsultBundle\Utility\RetrieveDoctorProfileUtil;
use ConsultBundle\Utility\RetrieveUserProfileUtil;
use ConsultBundle\Mapper\QuestionMapper;
use ConsultBundle\Manager\NotificationManager;

/**
 * Doctor Question Assignment manager
 */
class DoctorQuestionManager extends BaseManager
{
    protected $notification;

    /**
     * @param NotificationManager       $notification
     * @param RetrieveUserProfileUtil   $retrieveUserProfileUtil
     * @param RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil
     */
    public function __construct(NotificationManager $notification, RetrieveUserProfileUtil $retrieveUserProfileUtil, RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil)
    {
        $this->notification = $notification;
        $this->retrieveUserProfileUtil = $retrieveUserProfileUtil;
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
        parent::__construct($retrieveUserProfileUtil);
    }

    /**
     * @param integer $questionId - Id of the Question
     * @param array   $doctorsId  - Array of doctor practo_account_id
     *
     * @return null
     */
    public function setDoctorsForAQuestions($questionId, Array $doctorsId)
    {
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);
        foreach ($doctorsId as $doctorId) {
            $this->createDoctorQuestionEntity($question, $doctorId);
            $this->notification->createDoctorNotification($question, $doctorId);
        }

        $this->helper->persist(null, true);
    }

    /**
     * @param Array $updateData - data to be updated
     *
     * @return Question
     * @throws ValidationError
     */
    public function patch($updateData)
    {
        if (array_key_exists('id', $updateData) and array_key_exists('practo_account_id', $updateData)) {
            $practoAccountId = $updateData['practo_account_id'];
            /**
             * @var DoctorQuestion $question
             */
            $question = $this->helper->loadById($updateData['id'], ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME);
            if (empty($question) || $question->getPractoAccountId() != $practoAccountId) {
                throw new ValidationError(array("error"=>"Question is not mapped to this doctor."));
            }
        } else {
            throw new ValidationError(array("error"=> "practo_account_id and question_id is required."));
        }


        if (array_key_exists('reject', $updateData) && Utility::toBool($updateData['reject'])) {
            if ($question->getState() != 'UNANSWERED') {
                throw new ValidationError(array("error" => "The question is not unanswered"));
            }

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
        } elseif (array_key_exists('reject', $updateData) && $updateData['reject'] === false and array_key_exists('rejection_reason', $updateData)) {
            throw new ValidationError(array("error"=> "Please dont pass rejection_reason if reject is false"));
        }

        if (array_key_exists('view', $updateData) && Utility::toBool($updateData['view'])) {
            if (empty($question->getViewedAt())) {
                $question->setViewedAt(new \DateTime());
            }
        }

        $this->helper->persist($question, true);




        return  $this->fetchDetailDoctorQuestionObject($question, $practoAccountId);
    }

    /**
     * @param Integer $doctorQuestionId - Id of a doctor question object
     *
     * @return DoctorQuestion
     */
    public function loadById($doctorQuestionId)
    {

        $doctorQuestion =  $this->helper->loadById($doctorQuestionId, ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME);

        if (empty($doctorQuestion)) {
            return null;
        }

        return $this->fetchDetailDoctorQuestionObject($doctorQuestion, $_SESSION['authenticated_user']['id']);
    }

    /**
     * @param array $queryParams
     *
     * @return array|null
     * @throws \Exception
     */
    public function loadAllByDoctor($queryParams)
    {
        $doctorId = array_key_exists('practo_account_id', $queryParams) ? $queryParams['practo_account_id'] : -1;

        try {
            $doctorQuestionList = $this->getRepository()->findByFilters($doctorId, $queryParams);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if (null == $doctorQuestionList) {
            return null;
        }
        $doctorQuestionResponseList = QuestionMapper::mapDoctorQuestionList($doctorQuestionList['question'], true);

        return array("questions"=>$doctorQuestionResponseList, "count"=>$doctorQuestionList['count']);
    }

    /**
     * @param      $question    - Object of Question Entity
     * @param      $doctorId    - Doctor's Practo Account Id
     *
     * @return null
     */

    private function createDoctorQuestionEntity($question, $doctorId)
    {
        $doctorQuestion = new DoctorQuestion();
        $doctorQuestion->setQuestion($question);
        $doctorQuestion->setPractoAccountId($doctorId);
        $this->helper->persist($doctorQuestion);
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
        } catch (ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return;
    }

    private function getRepository()
    {

        return $this->helper->getRepository(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME);
    }
}
