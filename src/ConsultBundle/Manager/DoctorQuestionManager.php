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
<<<<<<< HEAD
     * @param NotificationManager       $notification
     * @param RetrieveUserProfileUtil   $retrieveUserProfileUtil
     * @param RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil
     */
    public function __construct(NotificationManager $notification, RetrieveUserProfileUtil $retrieveUserProfileUtil, RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil)
=======
     * @param RetrieveUserProfileUtil $retrieveUserProfileUtil
     * @param RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil
     */
    public function __construct(RetrieveUserProfileUtil $retrieveUserProfileUtil, RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil )
>>>>>>> master
    {
        $this->notification = $notification;
        $this->retrieveUserProfileUtil = $retrieveUserProfileUtil;
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
    }

    /**
<<<<<<< HEAD
     * @param integer $questionId - Id of the Question
     * @param array   $doctorsId  - Array of doctor practo_account_id
     *
     * @return null
     */
    public function setDoctorsForAQuestions($questionId, Array $doctorsId)
    {
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);
        foreach ($doctorsId as $doctorId) {
=======
     * @param $questionId
     * @param array $doctorsId
     */
    public function setDoctorsForAQuestions($questionId, Array $doctorsId)
    {
        $question = $this->helper->loadById($questionId, ConsultConstants::$QUESTION_ENTITY_NAME);
        foreach($doctorsId as $doctorId) {
>>>>>>> master
            $this->createDoctorQuestionEntity($question, $doctorId);
            $this->notification->createDoctorNotification($question, $doctorId);
        }

        $this->helper->persist(null, true);
    }

<<<<<<< HEAD
=======
    private function createDoctorQuestionEntity($question, $doctorId )
    {
        $doctorQuestion = new DoctorQuestion();
        $doctorQuestion->setQuestion($question);
        $doctorQuestion->setPractoAccountId($doctorId);
        $this->helper->persist($doctorQuestion);
    }

>>>>>>> master
    /**
     * @param Array $updateData - data to be updated
     *
     * @return Question
     * @throws ValidationError
     */
<<<<<<< HEAD
    public function patch($updateData)
    {
        if (array_key_exists('id', $updateData) and array_key_exists('practo_account_id', $updateData)) {
            $practoAccountId = $updateData['practo_account_id'];
=======
    public function patch($updateData) {

        if (array_key_exists('question_id', $updateData) and array_key_exists('practo_account_id', $updateData)) {
>>>>>>> master
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
          } else if (array_key_exists('reject', $updateData) && $updateData['reject'] === false and array_key_exists('rejection_reason', $updateData)) {
              throw new ValidationError(array("error"=> "Please dont pass rejection_reason if reject is false"));
          }

<<<<<<< HEAD
        if (array_key_exists('view', $updateData) && Utility::toBool($updateData['view'])) {
            if (empty($question->getViewedAt())) {
=======
        if (array_key_exists('view', $updateData) && $updateData['view'] == 'true') {
            if(!$question->getViewedAt()) {
>>>>>>> master
                $question->setViewedAt(new \DateTime());
            }
        }

        $this->helper->persist($question, true);




        return  $this->fetchDetailQuestionObject($question, $practoAccountId);
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

        return $this->fetchDetailQuestionObject($doctorQuestion, $_SESSION['authenticated_user']);
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
        } catch(ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return;
    }

<<<<<<< HEAD
    private function getRepository()
    {
=======
    public function loadById($doctorQuestionId){
>>>>>>> master

        return $this->helper->getRepository(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME);
    }

    /**
<<<<<<< HEAD
     * @param \ConsultBundle\Entity\DoctorQuestion $doctorQuestionEntity
     * @param                                      $practoAccountId
     *
     * @return \ConsultBundle\Response\DoctorQuestionResponseObject|null
     * @throws \HttpException
     */
    private function fetchDetailQuestionObject(DoctorQuestion $doctorQuestionEntity, $practoAccountId)
    {
        if (empty($doctorQuestionEntity)) {
            return null;
        }
        $questionEntity = $doctorQuestionEntity->getQuestion();

=======
     * @param $doctorId
     * @param null $queryParams
     * @return mixed
     */
    public function loadAllByDoctor($doctorId, $queryParams = null){
        return $this->getRepository()->findByFilters($doctorId, $queryParams);
    }

    private function getRepository() {
>>>>>>> master

        $question = null;

        if (!empty($questionEntity)) {
            if (!$questionEntity->getUserInfo()->isIsRelative()) {
                $this->retrieveUserProfileUtil->retrieveUserProfileNew($questionEntity->getUserInfo());
            }

            $question = new DoctorQuestionResponseObject($doctorQuestionEntity);

            /**
             * @var DoctorQuestionRepository $er
             */
            $er = $this->helper->getRepository(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME);
            $doctorQuestions = $er->findRepliesByQuestion($questionEntity);
            $replies = array();
            foreach ($doctorQuestions as $doctorQuestion) {
                $reply = new ReplyResponseObject();
                $reply->setAttributes($doctorQuestion);
                $doc = $this->retrieveDoctorProfileUtil->retrieveDoctorProfile($reply->getDoctorId());
                $reply->setDoctor($doc);
                $replies[] = $reply;
            }
            //var_dump(json_encode($questionEntity));die;

            $question->setReplies($replies);

            $bookmarkCount = $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME)->getBookmarkCountForAQuestion($questionEntity);
            $question->setBookmarkCount($bookmarkCount);
            $images = $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME)->getImagesForAQuestion($questionEntity);
            $question->setImages($images);

            //Set comments
            /**
             * @var QuestionCommentRepository $ecr
             */
            $ecr = $this->helper->getRepository(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME);
            $questionCommentList = $ecr->getComments($questionEntity, 10, 0, null);

            $question->setComments($questionCommentList);


        }

        return $question;
    }
}
