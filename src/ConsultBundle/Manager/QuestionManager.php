<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\UserInfo;
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
     */
    public function __construct(
        UserManager $userManager,
        Queue $queue,
        RetrieveUserProfileUtil $retrieveUserProfileUtil,
        RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil,
        QuestionBookmarkManager $questionBookmarkManager
    ) {
        $this->userManager = $userManager;
        $this->queue = $queue;
        $this->retrieveUserProfileUtil = $retrieveUserProfileUtil;
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
        $this->questionBookmarkManager = $questionBookmarkManager;
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
     *
     * @param array  $requestParams - parameters passed for creating new question object
     * @param string $profileToken  - profile token of the user
     * @return Question
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
            $job['tags'] = $requestParams['speciality'];
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

        $this->queue->setQueueName(Queue::DAA)->sendMessage(json_encode($job));

        return $question;
    }

    /**
     * @param array $requestParams - data for the updation
     * @throws ValidationError
     * @return Question
     */
    public function patch($requestParams)
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

        if (array_key_exists('view', $requestParams)) {
            $question->setViewCount($question->getViewCount() + 1);
            $this->helper->persist($question, 'true');
        }
        if (array_key_exists('share', $requestParams)) {
            $question->setShareCount($question->getShareCount() + 1);
            $this->helper->persist($question, 'true');
        }

        if (array_key_exists('bookmark', $requestParams)) {
            if (Utility::toBool($requestParams['bookmark'])) {
                try {
                    $questionBookmark = $this->questionBookmarkManager->add($requestParams);
                } catch (ValidationError $e) {
                    throw new ValidationError($e->getMessage());
                }

                return $questionBookmark;
            } else {
                try {
                    $this->questionBookmarkManager->remove($requestParams);
                } catch (ValidationError $e) {
                    throw new ValidationError($e->getMessage());
                }

               return 'Bookmark Deleted';
            } 
        }

        return $question;
    }

    /**
     * Load Question By Id
     *
     * @param integer $questionId - Question Id
     *
     * @return Question
     */
    public function load($questionId)
    {
        /**
         * @var Question $question
         */
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);

        if (is_null($question)) {
            return null;
        }

        $this->retrieveUserProfileUtil->loadUserDetailInQuestion($question);

        $this->retrieveDoctorProfileUtil->retrieveDoctorProfileForQuestion($question);



        return $question;
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
        $state = (array_key_exists('state', $request)) ? explode(",", $request['state']) : null;
        $category = (array_key_exists('category', $request)) ? explode(",", $request['category']) : null;
        $practoAccountId = (array_key_exists('practo_account_id', $request)) ? $request['practo_account_id'] : null;
        $bookmark = (array_key_exists('bookmark', $request)) ? $request['bookmark'] : null;

        $modifiedAfter = null;
        if (array_key_exists('modified_after', $request)) {
            $modifiedAfter = new \DateTime($request['modified_after']);
            $modifiedAfter->format('Y-m-d H:i:s');
        }

        $er =  $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME);
        $questionList = $er->findQuestionsByFilters($practoAccountId, $bookmark, $state, $category, $modifiedAfter, $limit, $offset);

        return $questionList;
    }

    /**
     * @param integer $questionId - Question Id
     * @param string  $state      - state of the question
     *
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
     * @param integer $questionId - Question Id
     * @param string  $tag        - text for the tag
     *
     */
    public function setTagByQuestionId($questionId, $tag)
    {
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);
        $tagObj = new QuestionTag();
        $tagObj->setTag($tag);
        $tagObj->setUserDefined(false);
        $tagObj->setQuestion($question);
        $question->addTag($tagObj);
        $this->helper->persist($question, 'true');
    }

    /**
     * @param Question $question - Question object
     * @param string $tags - text for the tags
     *
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
