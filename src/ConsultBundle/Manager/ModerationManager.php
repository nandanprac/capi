<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\UserInfo;
use ConsultBundle\Mapper\QuestionMapper1;
use ConsultBundle\Mapper\QuestionMapper;
use ConsultBundle\Repository\DoctorQuestionRepository;
use ConsultBundle\Repository\QuestionRepository;
use ConsultBundle\Response\DetailQuestionResponseObject;
use ConsultBundle\Response\ReplyResponseObject;
use ConsultBundle\Utility\RetrieveDoctorProfileUtil;
use ConsultBundle\Utility\RetrieveUserProfileUtil;
use ConsultBundle\Utility\Utility;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use ConsultBundle\Entity\Question;
use ConsultBundle\Entity\QuestionComment;
use ConsultBundle\Entity\QuestionImage;
use ConsultBundle\Entity\QuestionTag;
use ConsultBundle\Manager\ValidationError;
use ConsultBundle\Queue\AbstractQueue as Queue;



class ModerationManager extends BaseManager
{
    protected $userManager;
    protected $queue;
    protected $retrieveUserProfileUtil;
    protected $retrieveDoctorProfileUtil;

    /**
     * @param UserManager $userManager
     * @param Queue $queue
     * @param RetrieveUserProfileUtil $retrieveUserProfileUtil
     * @param RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil
     * @param QuestionBookmarkManager $questionBookmarkManager
     */
    public function __construct(
        UserManager $userManager,
        Queue $queue,
        RetrieveUserProfileUtil $retrieveUserProfileUtil,
        RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil,
        QuestionBookmarkManager $questionBookmarkManager
    )
    {
        $this->userManager = $userManager;
        $this->queue = $queue;
        $this->retrieveUserProfileUtil = $retrieveUserProfileUtil;
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
        $this->questionBookmarkManager = $questionBookmarkManager;
    }

    /**
     * Load Questions
     *
     * @param array $request - filters
     *
     * @return array Question objects
     */
    public function loadByFilters($request)
    {

        $limit = (array_key_exists('limit', $request)) ? $request['limit'] : 30;
        $offset = (array_key_exists('offset', $request)) ? $request['offset'] : 0;
        $state = "NEW";
        $category = (array_key_exists('category', $request)) ? explode(",", $request['category']) : null;
        $practoAccountId = (array_key_exists('practo_account_id', $request)) ? $request['practo_account_id'] : null;
        $bookmark = (array_key_exists('bookmark', $request)) ? $request['bookmark'] : null;

        $modifiedAfter = null;
        if (array_key_exists('modified_after', $request)) {
            $modifiedAfter = new \DateTime($request['modified_after']);
            $modifiedAfter->format('Y-m-d H:i:s');
        }
        /**
         * @var QuestionRepository $er
         */
        $er = $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME);

        $questionList = $er->findQuestionsByFilters($practoAccountId, $bookmark, $state, $category, $modifiedAfter, $limit, $offset);
        if (empty($questionList)) {
            return null;
        }

        $questionResponseList = QuestionMapper::mapQuestionList($questionList['questions']);

        $detailQuestions = array();

        $question = new Question();

        foreach ($questionResponseList as $baseQuestion) {

            $question = $this->load($baseQuestion->getId(), $practoAccountId);
            array_push($detailQuestions, $question);


        }

        return array("questions" => $detailQuestions, "count" => $questionList['count']);
    }


    /**
     * Load Question By Id
     *
     * @param integer $questionId - Question Id
     *
     * @param null $practoAccountId
     *
     * @return \ConsultBundle\Entity\Question
     */
    public function load($questionId, $practoAccountId = null)
    {
        /**
         * @var Question $question
         */
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);

        return $this->fetchDetailQuestionObject($question, $practoAccountId);
    }

    /**
     * @param \ConsultBundle\Entity\Question $questionEntity
     *
     * @param                                $practoAccountId
     *
     * @return \ConsultBundle\Response\DetailQuestionResponseObject
     * @throws \HttpException
     * @internal param int $practoAccountid
     *
     */
    private function fetchDetailQuestionObject(Question $questionEntity, $practoAccountId)
    {

        $question = null;
        if (!empty($questionEntity)) {
            if (!$questionEntity->getUserInfo()->isIsRelative()) {
                $this->retrieveUserProfileUtil->retrieveUserProfileNew($questionEntity->getUserInfo());
            }

            $question = new DetailQuestionResponseObject($questionEntity);

            /**
             * @var DoctorQuestionRepository $er
             */
            $er = $this->helper->getRepository(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME);
            $doctorQuestions = $er->findRepliesByQuestion($questionEntity, $practoAccountId);
            $replies = array();
            foreach ($doctorQuestions as $doctorQuestion) {
                $reply = new ReplyResponseObject();
                $reply->setAttributes($doctorQuestion);
                $doc = $this->retrieveDoctorProfileUtil->retrieveDoctorProfile($reply->getDoctorId());
                $reply->setDoctor($doc);
                $replies[] = $reply;
            }

            $question->setReplies($replies);

            $er = $this->helper->getRepository(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME);

            if (!empty($practoAccountId)) {
                $bookmark = $er->findOneBy(array("practoAccountId" => $practoAccountId,
                    "question" => $questionEntity,
                    "softDeleted" => 0));

                if (!empty($bookmark)) {
                    $question->setIsBookmarked(true);

                }
            }
        }

        return $question;
    }


    /**
     * @param array $requestParams - data for the updation
     * @throws ValidationError
     * @return Question
     */
        public function patchThis($requestParams)
    {
        $error = array();
        if (array_key_exists('question_id', $requestParams))
        {
            $question = new Question();
            $question = $this->helper->loadById($requestParams['question_id'], ConsultConstants::QUESTION_ENTITY_NAME);



            if (null === $question)
            {
                @$error['question_id'] = 'Question with this id does not exist';
                throw new ValidationError($error);
            }
        } else {
            @$error['question_id'] = 'This value cannot be blank';
            throw new ValidationError($error);
        }



        if(array_key_exists('state',$requestParams))
        {


            //// change this according to requirement conditions. hardcoding for new questions
            if($question->getState()!='NEW')
            {
                @$error['state'] = 'Not a New Question';
                throw new ValidationError($error);
            }
            else {
                if($requestParams['state'] == "ACCEPT")
                {

                    $question->setState("ACCEPTED");
                    $this->helper->persist($question,'true');
                    /////////////////////////////
                    $job = array();
                    $job['speciality'] = $question->getSpeciality();
                    $job['question_id'] = $question->getId();
                    $job['question'] = $question->getText();
                    $job['subject'] = $question->getSubject();
                    $this->queue->setQueueName(Queue::DAA)->sendMessage(json_encode($job));

                    ///////////////////////////////
                }
                elseif($requestParams['state'] == "REJECT")
                {
                    $question->setState("REJECTED");
                    $this->helper->persist($question,'true');
                }
                else{

                    @$error['state'] = 'Wrong values passed';
                    throw new ValidationError($error);
                }
            }
        }

        return $question;
    }

    /**
     * @param array   $requestParams   - parameters passed for updating question object

    * @return \ConsultBundle\Entity\Question
    * @throws \ConsultBundle\Manager\ValidationError
    */

//* @param string  $profileToken    - profile token of the user

    public function changeState($requestParams)
    {

        $question = new Question();
        $question= $this->patchThis($requestParams);
        return $question;
    }

}