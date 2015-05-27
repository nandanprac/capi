<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\UserInfo;
use ConsultBundle\Utility\RetrieveDoctorProfileUtil;
use ConsultBundle\Utility\RetrieveUserProfileUtil;
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

    /**
     * @param UserManager $userManager
     * @param UserProfileManager $userProfileManager
     * @param QuestionBookmarkManager $questionBookmarkManager
     * @param Queue $queue
     * @param RetrieveUserProfileUtil $retrieveUserProfileUtil
     * @param RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil
     */
    public function __construct(
        UserManager $userManager, UserProfileManager $userProfileManager, QuestionBookmarkManager $questionBookmarkManager,
        Queue $queue, RetrieveUserProfileUtil $retrieveUserProfileUtil, RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil )
    {
        $this->userManager = $userManager;
        $this->userProfileManager = $userProfileManager;
        $this->questionBookmarkManager = $questionBookmarkManager;
        $this->queue = $queue;
        $this->retrieveUserProfileUtil = $retrieveUserProfileUtil;
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
    }

    /**
     * @param $question
     * @param $requestParams
     * @throws ValidationError
     */
    public function updateFields($question, $requestParams)
    {
        if (array_key_exists('for_someone_else', $requestParams) and !empty($requestParams['for_someone_else'])) {
            $userProfileArray = $requestParams['for_someone_else'];
            $userProfile = $this->userProfileManager->add($userProfileArray);
            unset($requestParams['for_someone_else']);
        }

        if (array_key_exists('additional_info', $requestParams) and !empty($requestParams['additional_info'])) {
            $userInfoArray = $requestParams['additional_info'];
            if (array_key_exists('practo_account_id', $requestParams)) {
                $userInfoArray['practo_account_id'] = $requestParams['practo_account_id'];
            } else {
                $userInfoArray['practo_account_id'] = $question->getPractoAccountId();        //in case of patch
            }

            $userEntry = $this->userManager->add($userInfoArray);

            if (isset($userProfile)) {
                $userEntry->setUserProfileDetails($userProfile);
            }

            $question->setUserInfo($userEntry);
            unset($requestParams['additional_info']);
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
    public function add($requestParams)
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



    public function loadMultiple($requestData)
    {
        $error = array();
        if (!array_key_exists('question_id', $requestData))
            @$error['question_id']='This cannot be blank';
            throw new ValidationError($error);

        $questionId = $requestData['question_id'];
        $question = $this->helper->loadById($questionId, ConsultConstants::$QUESTION_ENTITY_NAME);

        if (is_null($question) or $question->isSoftDeleted())
            return null;
        return $question;
    }

    /**
     * Load all questions based on filters
     *
     * @param request parameters
     *
     * @return array
     */
    public function loadAll($modifiedAfter, $limit = 100, $offset = 0)
    {
        $questionList = array();
        $er =  $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
        $questionList = $er->findAllQuestions($modifiedAfter, $limit, $offset);

        if (is_null($questionList)) {
            return null;
        }
        return $questionList;
    }

    public function loadByFilters($request)
    {
        $limit = 100;
        $offset = 0;
        if (array_key_exists('limit', $request))
            $limit = $request['limit'];
        if (array_key_exists('offset', $request))
            $offset = $request['offset'];

        $modifiedAfter = null;
        if (array_key_exists('modified_after', $request)) {
            $modifiedAfter = new \DateTime($request['modified_after']);
            $modifiedAfter->format('Y-m-d H:i:s');
        }

        if (array_key_exists('state', $request))
            $questionList = $this->loadByState($request['state'], $modifiedAfter, $limit, $offset);

        if (array_key_exists('category', $request))
            $questionList = $this->loadByCategory(explode(",", $request['category']), $modifiedAfter, $limit, $offset);

        if (array_key_exists('practo_account_id', $request) and array_key_exists('bookmark', $request))
            $questionList = $this->loadByAccId($request['practo_account_id'], $request['bookmark'], $modifiedAfter, $limit, $offset);

        if (!isset($questionList))
            $questionList = $this->loadAll($modifiedAfter, $limit, $offset);

        return $questionList;
    }

    private function loadByAccId($practoAccountId, $bookmark, $modifiedAfter, $limit, $offset)
    {
        if ($bookmark == 0 or $bookmark == 2) {
            $questionList = array();
            $er =  $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
            $questionList = $er->findQuestionsByAccID($practoAccountId, $modifiedAfter, $limit, $offset);
        }
        if ($bookmark == 1 or $bookmark == 2) {
            $bookmarkList = array();
            $er =  $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
            $bookmarkList = $er->findBookmarksByAccID($practoAccountId, $modifiedAfter, $limit, $offset);
            $bookmarkQuestionList = array();
            foreach ($bookmarkList[0] as $bookMark)
                array_push($bookmarkQuestionList, $bookMark->getQuestion());
        }

        if ($bookmark == 0)
            if (is_null($questionList[0]))
                return null;
            else
                return $questionList;
        else if ($bookmark == 1)
            if (is_null($bookmarkList[0]))
                 return null;
             else
                return array($bookmarkQuestionList, $bookmarkList[1]);
        else if ($bookmark == 2)
            if (is_null($questionList[0]) and is_null($bookmarkList[0]))
                return null;
            else
                return array(array_merge($questionList[0], $bookmarkQuestionList), $questionList[1] + $bookmarkList[1]);
    }

    private function loadByState($state, $modifiedAfter, $limit, $offset)
    {
        $er =  $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
        $questionList = $er->findQuestionsByState($state, $modifiedAfter, $limit, $offset);

        if (is_null($questionList))
            return null;
        return $questionList;
    }

    private function loadByCategory($category, $modifiedAfter, $limit, $offset)
    {
        $er = $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
        $questionList = $er->findQuestionsByCategory($category, $modifiedAfter, $limit, $offset);

        if (is_null($questionList))
            return null;
        return $questionList;
    }

    public function setState($question_id, $state){
        $question = $this->helper->loadById($question_id, ConsultConstants::$QUESTION_ENTITY_NAME);

        $question->setState($state);
        $this->helper->persist($question, 'true');
    }

    public function setTagByQuestionId($question_id, $tag){
        $question = $this->helper->loadById($question_id, ConsultConstants::$QUESTION_ENTITY_NAME);
        $tagObj = new QuestionTag();
        $tagObj->setTag($tag);
        $tagObj->setUserDefined(False);
        $tagObj->setQuestion($question);
        $question->addTag($tagObj);
//        $this->helper->persist($tagObj, 'true');
        $this->helper->persist($question, 'true');
    }
}
