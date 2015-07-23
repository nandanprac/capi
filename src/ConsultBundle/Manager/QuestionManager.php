<?php

namespace ConsultBundle\Manager;

use FOS\RestBundle\Util\Codes;
use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\UserInfo;
use ConsultBundle\Mapper\QuestionMapper;
use ConsultBundle\Repository\DoctorQuestionRepository;
use ConsultBundle\Repository\QuestionCommentRepository;
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
use ConsultBundle\Entity\QuestionView;
use ConsultBundle\Manager\ValidationError;
use ConsultBundle\Queue\AbstractQueue as Queue;

/**
 * Question Manager
 */
class QuestionManager extends BaseManager
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
     * @param ClassificationManager     $classificationManager
     */
    public function __construct(
        UserManager $userManager,
        Queue $queue,
        RetrieveUserProfileUtil $retrieveUserProfileUtil,
        RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil,
        QuestionBookmarkManager $questionBookmarkManager,
        ClassificationManager $classificationManager
    ) {
        $this->userManager = $userManager;
        $this->queue = $queue;
        $this->retrieveUserProfileUtil = $retrieveUserProfileUtil;
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
        $this->questionBookmarkManager = $questionBookmarkManager;
        $this->classification = $classificationManager;
        parent::__construct($retrieveUserProfileUtil);
    }

    /**
     * @param Question $question      - question obect to be updated
     * @param array    $requestParams - data for the updation
     * @throws ValidationError
     */
    public function updateFields($question, $requestParams)
    {
        $question->setAttributes($requestParams);
        $question->setViewedAt(new \DateTime('now'));

        try {
            $this->validator->validate($question);
        } catch (ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return;
    }

    /**
     * @param array   $requestParams   - parameters passed for creating new question object
     * @param integer $practoAccountId - practo account id
     * @param string  $profileToken    - profile token of the user
     * @return \ConsultBundle\Entity\Question
     * @throws \ConsultBundle\Manager\ValidationError
     */
    public function add($requestParams, $practoAccountId, $profileToken = null)
    {
        $question = new Question();
        $question->setSoftDeleted(false);
        $job = array();
        if (array_key_exists('city', $requestParams)) {
            $job['city'] = $requestParams['city'];
        }
        if (array_key_exists('speciality', $requestParams)) {
            $job['speciality'] = $requestParams['speciality'];
        }

        $userInfoParams = array();
        if (array_key_exists('user_info', $requestParams)) {
            $userInfoParams = $requestParams['user_info'];
            unset($requestParams['user_info']);
        }
        $userInfoParams['practo_account_id'] = $practoAccountId;
        $userEntry = $this->userManager->add($userInfoParams, $profileToken);
        $question->setUserInfo($userEntry);

        $params = $this->validator->validatePostArguments($requestParams);
        $this->updateFields($question, $params);
        $this->helper->persist($question, 'true');

        $job['question_id'] = $question->getId();
        $job['question'] = $question->getText();
        $job['subject'] = $question->getSubject();

        //$this->queue->setQueueName(Queue::CLASSIFY)->sendMessage(json_encode($job));

        return $this->fetchDetailQuestionObject($question, $practoAccountId);
    }

    /**
     * @param array $requestParams
     * @param null  $practoAccountId
     *
     * @return \ConsultBundle\Response\DetailQuestionResponseObject|string
     * @throws \ConsultBundle\Manager\ValidationError
     * @throws \HttpException
     */
    public function patch($requestParams, $practoAccountId = null)
    {
        $error = array();
        if (array_key_exists('question_id', $requestParams)) {
            $question = $this->helper->loadById($requestParams['question_id'], ConsultConstants::QUESTION_ENTITY_NAME);
            if (null === $question) {
                @$error['question_id']='Question with this id does not exist';
                throw new ValidationError($error);
            }
        } else {
            @$error['question_id']='This value cannot be blank';
            throw new ValidationError($error);
        }

        if (array_key_exists('view', $requestParams) && Utility::toBool($requestParams['view'])) {
            $question->setViewCount($question->getViewCount() + 1);
            if (!empty($practoAccountId)) {
                $viewEntry = $this->helper->getRepository(ConsultConstants::QUESTION_VIEW_ENTITY_NAME)
                    ->findBy(array('question' => $question, 'practoAccountId' => $practoAccountId, 'softDeleted' => 0));
                if (empty($viewEntry)) {
                    $view = new QuestionView();
                    $view->setQuestion($question);
                    $view->setPractoAccountId($practoAccountId);
                    $question->addViews($view);
                }
                if ($practoAccountId == $question->getUserInfo()->getPractoAccountId()) {
                    $question->setViewedAt(new \DateTime('now'));
                }
            }
            $this->helper->persist($question, 'true');
        }

        if (array_key_exists('share', $requestParams)) {
            $question->setShareCount($question->getShareCount() + 1);
            $this->helper->persist($question, 'true');
        }

        if (array_key_exists('bookmark', $requestParams)) {
            if (empty($practoAccountId)) {
                throw new \HttpException('', Codes::HTTP_FORBIDDEN);
            }
            if (Utility::toBool($requestParams['bookmark'])) {
                try {
                    $questionBookmark = $this->questionBookmarkManager->add($requestParams);
                } catch (ValidationError $e) {
                    throw new ValidationError($e->getMessage());
                }

                //return $questionBookmark;
            } else {
                try {
                    $this->questionBookmarkManager->remove($requestParams);
                } catch (ValidationError $e) {
                    throw new ValidationError($e->getMessage());
                }

                return 'Bookmark Deleted';
            }
        }

        return $this->fetchDetailQuestionObject($question, $practoAccountId);
    }

    /**
     * Load Question By Id
     *
     * @param integer $questionId      - Question Id
     *
     * @param null    $practoAccountId
     *
     * @return \ConsultBundle\Entity\Question
     */
    public function load($questionId, $practoAccountId = null)
    {
        /**
         * @var QuestionRepository $er
         */
        $er =  $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME);

        /**
         * @var Question $question
         */
        $question = $er->findOneBy(array("id"=>$questionId, "softDeleted"=>0));


        if (empty($question)) {
            return null;
        }


        return $this->fetchDetailQuestionObject($question, $practoAccountId);
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
        /**
         * @var QuestionRepository $er
         */
        $er =  $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME);

        $limit = (array_key_exists('limit', $request)) ? $request['limit'] : 30;
        $offset = (array_key_exists('offset', $request)) ? $request['offset'] : 0;

        if (array_key_exists('search', $request) && !empty($request['search'])) {
            //$search = $this->classification->sentenceWords($request['search']);
            $search = preg_split('/\s+/', strtolower($request['search']));
            $questionList = $er->findSearchQuestions($search, $limit, $offset);
            if (empty($questionList)) {
                return null;
            }
            $questionResponseList = QuestionMapper::mapQuestionList($questionList['questions']);

            return array("questions" => $questionResponseList, "count" => $questionList['count']);
        }

        $state = (array_key_exists('state', $request)) ? explode(",", $request['state']) : null;
        $category = (array_key_exists('category', $request)) ? explode(",", $request['category']) : null;
        $practoAccountId = (array_key_exists('practo_account_id', $request)) ? $request['practo_account_id'] : null;
        $bookmark = (array_key_exists('bookmark', $request)) ? $request['bookmark'] : null;

        $modifiedAfter = null;
        if (array_key_exists('modified_after', $request)) {
            $modifiedAfter = new \DateTime($request['modified_after']);
            $modifiedAfter->format('Y-m-d H:i:s');
        }

        $questionList = $er->findQuestionsByFilters($practoAccountId, $bookmark, $state, $category, $modifiedAfter, $limit, $offset);
        if (empty($questionList)) {
            return null;
        }

        $questionResponseList = QuestionMapper::mapQuestionList($questionList['questions']);

        return array("questions" => $questionResponseList, "count" => $questionList['count']);
    }

    /**
     * @param int    $questionId
     * @param string $state
     *
     * @throws \Exception
     */
    public function setState($questionId, $state)
    {
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);
        if ($question) {
            $question->setState($state);
            $this->helper->persist($question, 'true');
        } else {
            throw new \Exception("Question with id ".$questionId." doesn't exist.");
        }
    }

    /**
     * @param int    $questionId
     * @param string $speciality
     *
     * @throws \Exception
     */
    public function setSpeciality($questionId, $speciality)
    {
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);
        if ($question) {
            $question->setSpeciality($speciality);
            $this->helper->persist($question, 'true');
        } else {
            throw new \Exception("Question with id ".$questionId." doesn't exist.");
        }
    }


    /**
     * @param integer $questionId - Question Id
     * @param string  $tags       - text for the tag
     *
     * @return mixed
     */
    public function setTagsByQuestionId($questionId, $tags)
    {
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);
        $this->setQuestionTags($question, $tags);
        $this->helper->persist($question, 'true');

        return $question;
    }

    /**
     * @param integer $id
     */
    public function delete($id)
    {
        /**
         * @var Question $question
         */
        $question = $this->helper->loadById($id, ConsultConstants::QUESTION_ENTITY_NAME);
        $question->setSoftDeleted(true);
        $this->helper->persist($question, true);

    }

    /**
     * @param Question $question - Question object
     * @param array    $tags     - text for the tags
     */
    private function setQuestionTags($question, $tags)
    {
        foreach ($tags as $tag) {
            $tagObj = new QuestionTag();
            $tagObj->setTag($tag);
            $tagObj->setUserDefined(true);
            $tagObj->setQuestion($question);
            $question->addTag($tagObj);
        }
    }
}
