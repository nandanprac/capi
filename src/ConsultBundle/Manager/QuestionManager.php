<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use ConsultBundle\Entity\Question;
use ConsultBundle\Entity\QuestionImage;
use ConsultBundle\Entity\QuestionBookmark;
use ConsultBundle\Entity\QuestionTag;
use ConsultBundle\Manager\ValidationError;

/**
 * Question Manager
 */
class QuestionManager extends BaseManager
{

    protected $questionImageManager;
    protected $questionBookmarkManager;

    /**
     * Constructor
     *
     * @param Doctrine                 $doctrine           - Doctrine
     * @param ValidatorInterface       $validator          - Validator
     */
    public function __construct(
        UserManager $userManager, QuestionBookmarkManager $questionBookmarkManager )
    {
        $this->userManager = $userManager;
        $this->questionBookmarkManager = $questionBookmarkManager;

    }

    /**
     * Update Fields
     *
     * @param Question $question     - PatientGrowth
     * @param array         $requestParams     - Request parameters
     *
     * @return null
     */
    public function updateFields($question, $requestParams)
    {
        /*if (array_key_exists('bookmark', $requestParams)) {
            if ($requestParams['bookmark']) {
                $data['bookmark'] = $requestParams['bookmark'];
                $data['practo_account_id'] = $requestParams['practo_account_id'];

                $questionBookmark = new questionBookmark;
                $questionBookmark->setQuestion($question);
                try {
                    $this->questionBookmarkManager->updateFields($questionBookmark, $data);
                    $question->addBookmark($questionBookmark);
                } catch (ValidationError $e) {
                    @$errors['bookmark'][$index + 1] = json_decode($e->getMessage(), true);
                }
                unset($requestParams['bookmark']);
            } else {
                //todo
            }
        }*/

        $userInfoParams= array('allergies', 'medications', 'prev_diagnosed_conditions', 'additional_details');
        $requestKeys = array_keys($requestParams);
        $userInfoArray = array();
        foreach($userInfoParams as $userInfo) {
            if (in_array($userInfo, $requestKeys)) {
                $userInfoArray[$userInfo] = $requestParams[$userInfo];
                unset($requestParams[$userInfo]);
            }
        }

        if (count($userInfoArray) > 0) {
            $userInfoArray['practo_account_id'] =  $requestParams['practo_account_id'];
            $userEntry = $this->userManager->add($userInfoArray);
            $question->setUserInfo($userEntry);
        }

        if (array_key_exists('tags', $requestParams)) {
            //$this->setQuestionTags($question, $requestParams['tags']);
            $this->setQuestionTags($question, explode(",", $requestParams['tags']));
            unset($requestParams['tags']);
        }
        $question->setAttributes($requestParams);
        $question->setModifiedAt(new \DateTime('now'));
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
        if (array_key_exists('state', $requestParams)) {
            unset($requestParams['state']);
        }
        $question = new Question();
        $question->setSoftDeleted(false);

        $this->updateFields($question, $requestParams);
        $this->helper->persist($question, 'true');

        return $question;
    }

    private function setQuestionTags($question, $tags)
    {
        foreach($tags as $tag)
        {
            $tagObj = new QuestionTag();
            $tagObj->setTag($tag);
            $tagObj->setUserDefined(True);
            $tagObj->setQuestion($question);
            $question->addTag($tagObj);
        }
    }

    public function patch($question, $requestParams)
    {
        if (array_key_exists('view', $requestParams)) {
            $question->setViewCount($question->getViewCount() + 1);
            unset($requestParams['view']);
        }
        if (array_key_exists('share', $requestParams)) {
            $question->setShareCount($question->getShareCount() + 1);
            unset($requestParams['share']);
        }
        if (array_key_exists('question_id', $requestParams)) {
            unset($requestParams['question_id']);
        }
        if (array_key_exists('_method', $requestParams)) {
            unset($requestParams['_method']);
        }
        if (array_key_exists('state', $requestParams)) {
            unset($requestParams['state']);
        }
        $this->updateFields($question, $requestParams);
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
        $question = $this->helper->loadById($questionId, ConsultConstants::$QUESTION_ENTITY_NAME);

        if (is_null($question) or $question->isSoftDeleted()) {
            return null;
        }

        return $question;
    }

    /**
     * Load all questions based on filters
     *
     * @param request parameters
     *
     * @return Question
     */
    public function loadAll($limit = 100, $offset = 0)
    {
        $questionList = $this->helper->getRepository(
            ConsultConstants::$QUESTION_ENTITY_NAME)->findBy(
            array('softDeleted' => 0),
            array('createdAt' => 'DESC'),
            $limit,
            $offset);

        if (is_null($questionList)) {
            return null;
        }

        return $questionList;
    }

    public function loadByFilters($request)
    {

        /* // setting default time to -> now - 6 months
        $from = new \DateTime('now');
        $from->sub(new \DateInterval('P1M'))->format('Y-m-d H:i:s');
        */

        $limit = 100;
        $offset = 0;
        if (array_key_exists('limit', $request)) {
            $limit = $request['limit'];
        }
        if (array_key_exists('offset', $request)) {
            $offset = $request['offset'];
        }

        if (array_key_exists('modified_at', $request)) {
            $from = new \DateTime($request['modified_at']);
            $from->format('Y-m-d H:i:s');
            $questionList = $this->loadByModifiedTime($from);
        }

        if (array_key_exists('state', $request)) {
            $questionList = $this->loadQuestionsByState($request['state'], $limit, $offset);
        }
        if (array_key_exists('category', $request)) {
            $questionList = $this->loadByCategory($request['category'], $limit, $offset);
        }

        if (array_key_exists('practo_account_id', $request) and array_key_exists('bookmark', $request)) {
            $questionList = $this->loadByAccId($request['practo_account_id'], $request['bookmark'], $limit, $offset);
        }

        if (!isset($questionList)) {
            $questionList = $this->loadAll();
        }

        return $questionList;
    }

    private function loadByAccId($practoAccountId, $bookmark, $limit, $offset)
    {
        $questionList = $this->helper->getRepository(
            ConsultConstants::$QUESTION_ENTITY_NAME)->findBy(
            array('practoAccountId' => $practoAccountId),
            array('createdAt' => 'DESC'),
            $limit,
            $offset);

        $bookmarkList = $this->helper->getRepository(
            ConsultConstants::$QUESTION_BOOKMARK_ENTITY_NAME)->findBy(
            array('practoAccountId' => $practoAccountId),
            array(),
            $limit,
            $offset);

        $bookmarkQuestionList = array();
        foreach ($bookmarkList as $bookMark) {
            array_push($bookmarkQuestionList, $bookMark->getQuestion());
        }

        if (is_null($questionList) and is_null($bookmarkList)) {
            return null;
        }
        if ($bookmark == 0)
            return $questionList;
        else if ($bookmark == 1)
            return $bookmarkQuestionList;
        else if ($bookmark == 2)
            return array_merge($questionList, $bookmarkQuestionList);
    }

    private function loadByModifiedTime($modifiedAt)
    {
        $er =  $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
        $questionList = $er->findQuestionsByModifiedTime($modifiedAt);

        return  $questionList;
    }

    private function loadQuestionsByState($state, $limit, $offset)
    {
        $questionList = $this->helper->getRepository(
            ConsultConstants::$QUESTION_ENTITY_NAME)->findBy(
            array('state' => $state, 'softDeleted' => 0),
            array('createdAt' => 'DESC'),
            $limit,
            $offset);

        if (is_null($questionList)) {
            return null;
        }

        return $questionList;
    }

    private function loadByCategory($category, $limit, $offset)
    {
        $er = $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
        $questionList = $er->findQuestionsByCategory($category, $limit, $offset);

        return $questionList;
    }

}
