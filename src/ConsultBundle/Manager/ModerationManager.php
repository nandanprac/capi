<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\UserInfo;
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
use ConsultBundle\DQL\Day;
use ConsultBundle\DQL\Month;
use ConsultBundle\DQL\Year;
use ConsultBundle\DQL\Week;

/**
 * Dashboard manager
 */
class ModerationManager extends BaseManager
{
    protected $userManager;
    protected $queue;
    protected $retrieveUserProfileUtil;
    protected $retrieveDoctorProfileUtil;

    /**
     * @param UserManager               $userManager
     * @param Queue                     $queue
     * @param RetrieveUserProfileUtil   $retrieveUserProfileUtil
     * @param RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil
     * @param QuestionBookmarkManager   $questionBookmarkManager
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
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
        $this->questionBookmarkManager = $questionBookmarkManager;
        parent::__construct($retrieveUserProfileUtil);
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

        $er = $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME);

        $state = (array_key_exists('state', $request)) ? $request['state'] : null;//"NEW";
        $thisMonth=(array_key_exists('thisMonth', $request)) ? $request['thisMonth'] : false;
        $lastMonth=(array_key_exists('lastMonth', $request)) ? $request['lastMonth'] : false;
        $startDate=(array_key_exists('startDate', $request)) ? $request['startDate'] : null;
        $offset=(array_key_exists('offset', $request)) ? $request['offset'] : null;
        $endDate=(array_key_exists('endDate', $request)) ? $request['endDate'] : null;
        $thisYear=(array_key_exists('thisYear', $request)) ? $request['thisYear'] : false;
        $limit=(array_key_exists('limit', $request)) ? $request['limit'] : null;
        $patientId=(array_key_exists('patientID', $request)) ? $request['patientID'] : null;
        $questionID=(array_key_exists('questionID', $request)) ? $request['questionID'] : null;
       // $patientEmail=(array_key_exists('patientID',$request)) ? $request['patientID'] : null;
        $patientName=(array_key_exists('patientName', $request)) ? $request['patientName'] : null;



        if (array_key_exists('search', $request)) {
            //$search = $this->classification->sentenceWords($request['search']);
            $search = preg_split('/\s+/', strtolower($request['search']));
            $questionList = $er->findSearchQuestions($search, $limit, $offset);
            if (empty($questionList)) {
                return null;
            }
            $questionResponseList = QuestionMapper::mapQuestionList($questionList['questions']);

            $detailQuestions = array();


            foreach ($questionResponseList as $baseQuestion) {
                $question = $this->load($baseQuestion->getId());
                $quesArr=QuestionMapper::mapToModerationArray($question);
                array_push($detailQuestions, $quesArr);
            }

            return array("questions" => $detailQuestions, "count" => $questionList['count']);
        }

        $questionList = $er->findModerationQuestionsByFilters($thisMonth, $lastMonth, $state, $startDate, $endDate, $thisYear, $limit, $patientId, $patientName, $questionID);
        if (empty($questionList)) {
            return null;
        }

        $questionResponseList = QuestionMapper::mapQuestionList($questionList['questions']);

        $detailQuestions = array();
        $commentsArr = array();


        foreach ($questionResponseList as $baseQuestion) {
            $question = $this->load($baseQuestion->getId());
            $comments = $this->getComments($baseQuestion->getId());

            $quesArr=QuestionMapper::mapToModerationArray($question);

            array_push($detailQuestions, $quesArr);
            if ($comments['questionId'] != null) {
                array_push($commentsArr, $comments);
            }
        }

        $er1 = $this->helper->getRepository(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME);
        $counts = $er1->findDoctorQuestionCounts($thisMonth, $lastMonth, $state, $startDate, $endDate, $thisYear, $limit, $patientId, $patientName, $questionID);
        $questionList['count']['viewedCount'] = intval($counts[0]['view_count']);
        $questionList['count']['ratedCount'] = intval($counts[0]['rated_count']);

        return array("questions" => $detailQuestions, "count" => $questionList['count'], "comments"=>$commentsArr);
    }


    /**
     * @return array SummaryCounts
     */
    public function loadSummary()
    {
        $er = $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME);
        $totalCount=$er->totalCount();
        $thisMonthCount=$er->thisMonthCount();
        $prevMonthCount=$er->lastMonthCount();

        return array('totalQuestions' => $totalCount, 'thisMonthQuestions' => $thisMonthCount, 'lastMonthQuestions' => $prevMonthCount);

    }


    /**
     * @param array $request - dates
     *
     * @return int count between dates
     */
    public function loadCustomCount($request)
    {
        $startDate = $request['start'];
        $endDate = $request['end'];
        $er = $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME);

        return $er->customCount($startDate, $endDate);

    }

    /**
     * Load Question By Id
     *
     * @param integer $questionId - Question Id
     * @return \ConsultBundle\Response\DetailQuestionResponseObject
     */
    public function load($questionId)
    {
        /**
         * @var Question $question
         */
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);

        return $this->fetchDetailQuestionObject($question, $question->getUserInfo()->getPractoAccountId());
    }


    /**
     * @param array $requestParams - data for the updation
     * @throws ValidationError
     * @return Question
     */
    public function changeState($requestParams)
    {
        $error = array();
        if (array_key_exists('question_id', $requestParams)) {
            $question = new Question();
            $question = $this->helper->loadById($requestParams['question_id'], ConsultConstants::QUESTION_ENTITY_NAME);

            if (null === $question) {
                @$error['question_id'] = 'Question with this id does not exist';
                throw new ValidationError($error);
            }
        } else {
            @$error['question_id'] = 'This value cannot be blank';
            throw new ValidationError($error);
        }

        if (array_key_exists('state', $requestParams)) {
        //// change this according to requirement conditions. hardcoding for new questions
            if ($question->getState() != 'NEW') {
                @$error['state'] = 'Not a New Question';
                throw new ValidationError($error);
            } else {
                if ($requestParams['state'] == "ACCEPT") {
                    $question->setState("ACCEPTED");
                    $this->helper->persist($question, 'true');
                    $job = array();

                    $job['speciality'] = $question->getSpeciality();
                    $job['question_id'] = $question->getId();
                    $job['question'] = $question->getText();
                    $job['subject'] = $question->getSubject();
                    $this->queue->setQueueName(Queue::CLASSIFY)->sendMessage(json_encode($job));

                } elseif ($requestParams['state'] == "REJECT") {
                    $question->setState("REJECTED");
                    $this->helper->persist($question, 'true');
                } else {
                    @$error['state'] = 'Wrong values passed';
                    throw new ValidationError($error);
                }
            }
        }

        return $question;
    }


    /**
     * @param integer $questionId
     * @return array
     */
    public function getComments($questionId)
    {
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);
        $er=$this->helper->getRepository(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME);
        $replies=$er->getModerationComments($question);

        return array('questionId' => $replies['questionID'], 'comments' => $replies['comments'], 'replyCount' => $replies['count']);
    }

    /**
     * @param integer $commentID
     */
    public function softDeleteComment($commentID)
    {
        $comment = $this->helper->loadById($commentID, ConsultConstants::QUESTION_COMMENT_ENTITY_NAME);
        if (null === $comment) {
            @$error['commentID'] = 'Comment with this id does not exist';
            throw new ValidationError($error);
        }
        $comment->setsoftDeleted(1);
        $this->helper->persist($comment, 'true');
    }

    /**
     * @param integer $flagID
     */
    public function softDeleteFlag($flagID)
    {
        $flag = $this->helper->loadById($flagID, ConsultConstants::QUESTION_COMMENT_FLAG_ENTITY_NAME);
        if (null === $flag) {
            @$error['flagID'] = 'Flag with this id does not exist';
            throw new ValidationError($error);
        }
        $flag->setsoftDeleted(1);
        $this->helper->persist($flag, 'true');
    }

    /**
     * @param array $request
     * @return array
     */
    public function getDoctorDetails($request)
    {
        $startDate=(array_key_exists('startDate', $request)) ? $request['startDate'] : null;
        $endDate=(array_key_exists('endDate', $request)) ? $request['endDate'] : null;

        $er = $this->helper->getRepository(ConsultConstants::DOCTOR_SETTING_ENTITY_NAME);
        $details = $er->getDoctorDetails($startDate, $endDate);

        return $details;
    }
}