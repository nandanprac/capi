<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 16:50
 */

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\DoctorQuestion;
use ConsultBundle\Entity\Question;
use ConsultBundle\Helper\Helper;
use ConsultBundle\Repository\DoctorQuestionRepository;
use ConsultBundle\Repository\QuestionCommentRepository;
use ConsultBundle\Response\DetailQuestionResponseObject;
use ConsultBundle\Response\DoctorQuestionResponseObject;
use ConsultBundle\Response\ReplyResponseObject;
use ConsultBundle\Utility\RetrieveUserProfileUtil;
use ConsultBundle\Validator\Validator;
use ConsultBundle\Entity\BaseEntity;

/**
 * Class BaseManager
 *
 * @package ConsultBundle\Manager
 */
class BaseManager
{
    private $userProfileUtil;

    /**
     * @var Helper $helper
     */
    protected $helper;

    /**
     * validator
     * @var Validator
     */
    protected $validator;

    /**
     * @param \ConsultBundle\Utility\RetrieveUserProfileUtil $userProfileUtil
     */
    public function __construct(RetrieveUserProfileUtil $userProfileUtil = null)
    {
        $this->userProfileUtil = $userProfileUtil;
    }

    /**
     * @param  Validator $validator
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param  Helper $helper
     */
    public function setHelper(Helper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param BaseEntity $entity
     */
    public function validate($entity)
    {
        $this->validator->validate($entity);
    }

    /**
     * @param \ConsultBundle\Entity\Question                       $questionEntity
     * @param                                                      $practoAccountId
     * @param \ConsultBundle\Response\DetailQuestionResponseObject $question
     *
     * @return \ConsultBundle\Response\DetailQuestionResponseObject|null
     * @throws \HttpException
     */
    protected function fetchDetailQuestionObject(Question $questionEntity, $practoAccountId, DetailQuestionResponseObject $question = null)
    {

        if (!empty($questionEntity) && empty($question)) {
            if (!$questionEntity->getUserInfo()->isIsRelative()) {
                $this->userProfileUtil->retrieveUserProfileNew($questionEntity->getUserInfo());
            }

            $question = new DetailQuestionResponseObject($questionEntity, $practoAccountId);
        }


        $this->fetchRepliesByQuestion($questionEntity, $question);

        $bookmarkCount = $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME)->getBookmarkCountForAQuestion($questionEntity);
        $question->setBookmarkCount($bookmarkCount);

        $er = $this->helper->getRepository(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME);

        //Set comments
        /**
         * @var QuestionCommentRepository $ecr
         */
        $ecr = $this->helper->getRepository(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME);
        $questionCommentList = $ecr->getComments($questionEntity, 10, 0, $practoAccountId);

        $question->setComments($questionCommentList);

        if (!empty($practoAccountId)) {
            $bookmark = $er->findOneBy(array("practoAccountId" => $practoAccountId,
                "question" => $questionEntity,
                "softDeleted" => 0));

            if (!empty($bookmark)) {
                $question->setIsBookmarked(true);

            }
        }

        return $question;
    }




    /**
     * @param \ConsultBundle\Entity\DoctorQuestion $doctorQuestionEntity
     * @param                                      $practoAccountId
     *
     * @return \ConsultBundle\Response\DoctorQuestionResponseObject|null
     * @throws \HttpException
     */
    protected function fetchDetailDoctorQuestionObject(DoctorQuestion $doctorQuestionEntity, $practoAccountId)
    {
        if (empty($doctorQuestionEntity)) {
            return null;
        }
        $questionEntity = $doctorQuestionEntity->getQuestion();
        $question = null;
        if (!empty($questionEntity)) {
            if (!$questionEntity->getUserInfo()->isIsRelative()) {
                $this->userProfileUtil->retrieveUserProfileNew($questionEntity->getUserInfo());
            }
        }

        $question = new DoctorQuestionResponseObject($doctorQuestionEntity);

        $this->fetchDetailQuestionObject($questionEntity, $practoAccountId, $question);

        $images = $this->helper->getRepository(ConsultConstants::QUESTION_ENTITY_NAME)->getImagesForAQuestion($questionEntity);
        $question->setImages($images);


        return $question;
    }


    /**
     * @param \ConsultBundle\Entity\Question                       $questionEntity
     * @param \ConsultBundle\Response\DetailQuestionResponseObject $question
     *
     * @throws \HttpException
     */
    protected function fetchRepliesByQuestion(Question $questionEntity, DetailQuestionResponseObject $question)
    {
        /**
         * @var DoctorQuestionRepository $er
         */
        $er = $this->helper->getRepository(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME);
        $doctorQuestions = $er->findRepliesByQuestion($questionEntity);
        $replies = array();
        foreach ($doctorQuestions as $doctorQuestion) {
            $reply = new ReplyResponseObject();
            $reply->setAttributes($doctorQuestion);
            $reply->setDoctorFromAttributes($doctorQuestion);
            $replies[] = $reply;
        }

        $question->setReplies($replies);

    }
}
