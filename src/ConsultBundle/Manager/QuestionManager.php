<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\UserInfo;
use ConsultBundle\Utility\RetrieveDoctorProfileUtil;
use ConsultBundle\Utility\RetrieveUserProfileUtil;
use ConsultBundle\Utility\UpdateAccountsUtil;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use ConsultBundle\Entity\Question;
use ConsultBundle\Entity\QuestionComment;
use ConsultBundle\Entity\QuestionImage;
use ConsultBundle\Entity\QuestionBookmark;
use ConsultBundle\Entity\QuestionTag;
use ConsultBundle\Manager\ValidationError;
use ConsultBundle\Queue\AbstractQueue as Queue;

/**
 * Question Manager
 */
class QuestionManager extends BaseManager
{

    protected $questionBookmarkManager;
    protected $userManager;
    protected $userProfileManager;
    protected $queue;
    protected $retrieveUserProfileUtil;
    protected $retrieveDoctorProfileUtil;
    protected $updateAccountsUtil;

    /**
     * @param UserManager $userManager
     * @param UserProfileManager $userProfileManager
     * @param QuestionBookmarkManager $questionBookmarkManager
     * @param Queue $queue
     * @param RetrieveUserProfileUtil $retrieveUserProfileUtil
     * @param RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil
     * @param UpdateAccountsUtil $updateAccountsUtil
     */
    public function __construct(
        UserManager $userManager, UserProfileManager $userProfileManager, QuestionBookmarkManager $questionBookmarkManager,
        Queue $queue, RetrieveUserProfileUtil $retrieveUserProfileUtil, RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil,
        UpdateAccountsUtil $updateAccountsUtil)
    {
        $this->userManager = $userManager;
        $this->userProfileManager = $userProfileManager;
        $this->questionBookmarkManager = $questionBookmarkManager;
        $this->queue = $queue;
        $this->retrieveUserProfileUtil = $retrieveUserProfileUtil;
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
        $this->updateAccountsUtil = $updateAccountsUtil;
    }

    /**
     * @param $question
     * @param $requestParams
     * @throws ValidationError
     */
    public function updateFields($question, $requestParams)
    {
        if (array_key_exists('user_profile_details', $requestParams)) {
            if (array_key_exists('is_someone_else', $requestParams['user_profile_details']) and
                $requestParams['user_profile_details']['is_someone_else'] == true) {
                $userProfileArray = $requestParams['user_profile_details'];
                unset($userProfileArray['is_someone_else']);
                $userProfile = $this->userProfileManager->add($userProfileArray);
                unset($requestParams['user_profile_details']);
            }
        }

        if (array_key_exists('additional_info', $requestParams) or isset($userProfile)) {
            $userInfoArray = array();
            if (array_key_exists('additional_info', $requestParams) and !empty($requestParams['additional_info'])) {
                $userInfoArray = $requestParams['additional_info'];
                unset($requestParams['additional_info']);
            }
            if (array_key_exists('practo_account_id', $requestParams))
                $userInfoArray['practo_account_id'] = $requestParams['practo_account_id'];
            else
                $userInfoArray['practo_account_id'] = $question->getPractoAccountId();        //in case of patch

            $userEntry = $this->userManager->add($userInfoArray);

            if (isset($userProfile)) {
                $userEntry->setUserProfileDetails($userProfile);
            }

            $question->setUserInfo($userEntry);
        }

        if (array_key_exists('tags', $requestParams)) {
            $this->setQuestionTags($question, explode(",", $requestParams['tags']));
            unset($requestParams['tags']);
        }

        $question->setAttributes($requestParams);
        $question->setViewedAt(new \DateTime('now'));

        try {
            $this->validator->validate($question);
        } catch(ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return;
    }

    /**
     *
     * @param array $requestParams
     *
     * @return Question
     */
    public function add($requestParams, $profileToken = null)
    {
        $question = new Question();
        $question->setSoftDeleted(false);
        $job = array();
        if (array_key_exists('city', $requestParams)) {
            $job['city'] = $requestParams['city'];
        }
        if (array_key_exists('tags', $requestParams)) {
            $job['tags'] = $requestParams['tags'];
        }
        $params = $this->validator->validatePostArguments($requestParams);

        $this->updateFields($question, $params);

        $this->updateAccountsUtil->updateAccountDetails($profileToken, $params);

        $this->helper->persist($question, 'true');

        $job['question_id'] = $question->getId();
        $job['question'] = $question->getText();

        $this->queue->setQueueName(Queue::DAA)->sendMessage(json_encode($job));

        return $question;
    }

    private function setQuestionTags($question, $tags)
    {
        foreach($tags as $tag) {
            $tagObj = new QuestionTag();
            $tagObj->setTag($tag);
            $tagObj->setUserDefined(True);
            $tagObj->setQuestion($question);
            $question->addTag($tagObj);
        }
    }

    public function patch($requestParams)
    {
        $error = array();
        if (array_key_exists('question_id', $requestParams)) {
            $question = $this->load($requestParams['question_id']);
            if (null === $question)
                throw new ValidationError();
        } else {
            @$error['question_id']='This cannot be blank';
            throw new ValidationError($error);
        }

        if (array_key_exists('view', $requestParams))
            $question->setViewCount($question->getViewCount() + 1);
        if (array_key_exists('share', $requestParams))
            $question->setShareCount($question->getShareCount() + 1);

        if (array_key_exists('comment', $requestParams)) {
            $commentParams = array();
            if (array_key_exists('practo_account_id', $requestParams))
                 $commentParams['practo_account_id'] = $requestParams['practo_account_id'];
            if (array_key_exists('c_text', $requestParams))
                 $commentParams['text'] = $requestParams['c_text'];

            $questionComment = new QuestionComment();
            $questionComment->setAttributes($commentParams);
            $questionComment->setQuestion($question);
            $question->addComment($questionComment);
            try {
                $this->validator->validateComment($questionComment);
            } catch(ValidationError $e) {
                throw new ValidationError($e->getMessage());
            }
        }

        $params = $this->validator->validatePatchArguments($requestParams);
        $this->updateFields($question, $params);
        $this->helper->persist($question, 'true');

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
        $question = $this->helper->loadById($questionId, ConsultConstants::$QUESTION_ENTITY_NAME);

        if (is_null($question))
            return null;

        $this->retrieveUserProfileUtil->loadUserDetailInQuestion($question);

        $this->retrieveDoctorProfileUtil->retrieveDoctorProfileForQuestion($question);



        return $question;
    }




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

       $er =  $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
       $questionList = $er->findQuestionsByFilters($practoAccountId, $bookmark, $state, $category, $modifiedAfter, $limit, $offset); 

       return $questionList;
    }

    public function setState($question_id, $state){
        $question = $this->helper->loadById($question_id, ConsultConstants::$QUESTION_ENTITY_NAME);
	if ($question){
	    $question->setState($state);
            $this->helper->persist($question, 'true');
	} else {
	    throw new \Exception("Question with id ".$question_id." doesn't exist.");
	}
    }

    public function setTagByQuestionId($question_id, $tag){
        $question = $this->helper->loadById($question_id, ConsultConstants::$QUESTION_ENTITY_NAME);
        $tagObj = new QuestionTag();
        $tagObj->setTag($tag);
        $tagObj->setUserDefined(False);
        $tagObj->setQuestion($question);
        $question->addTag($tagObj);
        $this->helper->persist($question, 'true');
    }
}
