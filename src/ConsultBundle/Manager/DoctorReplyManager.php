<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 18:04
 */

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\DoctorReplyFlag;
use ConsultBundle\Manager\NotificationManager;
use ConsultBundle\Entity\DoctorQuestion;
use ConsultBundle\Entity\DoctorReply;
use ConsultBundle\Entity\DoctorReplyRating;
use ConsultBundle\Entity\DoctorReplyVote;
use ConsultBundle\Repository\DoctorQuestionRepository;
use ConsultBundle\Response\ReplyResponseObject;
use ConsultBundle\Utility\Utility;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Util\Codes;
use ConsultBundle\Queue\AbstractQueue as Queue;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class DoctorReplyManager
 *
 * @package ConsultBundle\Manager
 */
class DoctorReplyManager extends BaseManager
{
    public static $mandatoryFields;

    public static $mandatoryFieldsForPostReply = array(
        "practo_account_id",
        "doctor_question_id",
        "text",
    );

    /**
     * @param \ConsultBundle\Queue\AbstractQueue         $queue
     * @param \ConsultBundle\Manager\NotificationManager $notification
     */
    public function __construct(Queue $queue, NotificationManager $notification)
    {
        if (!isset(self::$mandatoryFields)) {
            self::$mandatoryFields = array("id");
        }
        $this->queue = $queue;
        $this->notification = $notification;
    }

    /**
     * @param array $postData
     *
     * @return \ConsultBundle\Entity\DoctorReply
     * @throws \HttpException
     */
    public function replyToAQuestion($postData)
    {
        $this->helper->checkForMandatoryFields(self::$mandatoryFieldsForPostReply, $postData);
        $practoAccountId = $postData['practo_account_id'];
        $doctorQuestionId = $postData['doctor_question_id'];
        $answerText = $postData['text'];

        $doctorReply = new DoctorReply();
        /**
         * @var DoctorQuestion $doctorQuestion
         */
        $doctorQuestion = $this->helper->loadById(
            $doctorQuestionId,
            ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME
        );

        if (is_null($doctorQuestion->getQuestion())) {
            throw new \HttpException("Error:Doctor has not been assigned the question", Codes::HTTP_BAD_REQUEST);
        }

        if ($practoAccountId != $doctorQuestion->getPractoAccountId()) {
            throw new \HttpException("You are not allowed to perform this operation", Codes::HTTP_FORBIDDEN);
        }

        if ($doctorQuestion->getRejectedAt()) {
            throw new \HttpException("The question has been rejected", Codes::HTTP_BAD_REQUEST);
        }

        if ($doctorQuestion->getState() != "UNANSWERED") {
            throw new \HttpException("The doctor is not allowed to answer this question", Codes::HTTP_BAD_REQUEST);
        }

        $doctorQuestion->setState("ANSWERED");
        $doctorQuestion->getQuestion()->setState("ANSWERED");
        $doctorReply->setDoctorQuestion($doctorQuestion);
        $doctorReply->setText($answerText);
        $this->helper->persist($doctorReply, true);

        $this->notification
            ->createPatientNotification($doctorQuestion->getQuestion()->getId(), $doctorQuestion->getQuestion()->getUserInfo()->getPractoAccountId(), "Your Query has been answered");

        $this->queue->setQueueName(Queue::CONSULT_GCM)
            ->sendMessage(
                json_encode(
                    array(
                        "type"=>"consult",
                        "message"=>array(
                            'text'=>"Your Query has been answered",
                            'question_id'=>$doctorQuestion->getQuestion()->getId(),
                            'subject'=>$doctorQuestion->getQuestion()->getSubject(),
                            'consult_type'=>ConsultConstants::PUBLIC_QUESTION_NOTIFICATION_TYPE,
                        ),
                        "send_to"=>"fabric",
                        "account_ids"=>array($doctorQuestion->getQuestion()->getUserInfo()->getPractoAccountId()),
                    )
                )
            );


        return new ReplyResponseObject($doctorReply);
    }

    /**
     * @param array $postData
     *
     * @return \ConsultBundle\Response\ReplyResponseObject
     * @throws \ConsultBundle\Manager\ValidationError
     * @throws \HttpException
     */
    public function patchDoctorReply($postData)
    {


        $practoAccountId = $postData['practo_account_id'];

        if (array_key_exists("doctor_reply", $postData)) {
            $doctorReply = $postData['doctor_reply'];
        } else {
            throw new \HttpException("doctor_reply is mandatory", Codes::HTTP_BAD_REQUEST);
        }

        $changed = false;

        $this->helper->checkForMandatoryFields(self::$mandatoryFields, $doctorReply);

        //Fetch Doctor Reply
        $id = $doctorReply['id'];

        /**
         * @var DoctorReply $doctorReplyEntity
         */
        $doctorReplyEntity = $this->helper->loadById($id, ConsultConstants::DOCTOR_REPLY_ENTITY_NAME);
        if (empty($doctorReplyEntity)) {
            throw new \HttpException("Not a valid Doctor Reply Id", Codes::HTTP_BAD_REQUEST);
        }

        $ownerId = $doctorReplyEntity->getDoctorQuestion()->getQuestion()->getUserInfo()->getPractoAccountId();

        //Mark As Best Answer
        if (array_key_exists("rating", $doctorReply)) {
            if ($ownerId != $practoAccountId) {
                throw new \HttpException("Only the one who has asked the question can rate it", Codes::HTTP_BAD_REQUEST);
            }

            $doctorReplyEntity->setRating($doctorReply['rating']);
            $changed = true;

            $this->queue->setQueueName(Queue::CONSULT_GCM)
                ->sendMessage(
                    json_encode(
                        array(
                        "type"=>"consult",
                        "message"=>array(
                        'text'=>"Your answer has been rated by the Asker",
                        'question_id'=>$doctorReplyEntity->getDoctorQuestion()->getQuestion()->getId(),
                         'subject'=>$doctorReplyEntity->getDoctorQuestion()->getQuestion()->getSubject(),
                        'consult_type'=>ConsultConstants::PUBLIC_QUESTION_NOTIFICATION_TYPE,
                        ),
                        "send_to"=>"synapse",
                        "account_ids"=>array($doctorReplyEntity->getDoctorQuestion()->getPractoAccountId()),
                        )
                    )
                );
        }

        //Mark the answer as viewed
        if (array_key_exists("viewed", $doctorReply)) {
            if ($ownerId != $practoAccountId) {
                throw new \HttpException("Not the owner of the question", Codes::HTTP_BAD_REQUEST);
            }

            if (!$doctorReplyEntity->getViewedAt()) {
                $doctorReplyEntity->setViewedAt(new \DateTime());
                $changed = true;
            }
        }

        //Vote
        if (array_key_exists("vote", $doctorReply)) {
            $vote = $doctorReply['vote'];
            $er = $this->helper->getRepository(ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY);


            $doctorReplyVoteEntity = $er->findOneBy(
                array("practoAccountId" => $practoAccountId,
                    "reply" => $doctorReplyEntity,

                )
            );


            if (!$doctorReplyVoteEntity) {
                $doctorReplyVoteEntity = new DoctorReplyVote();
                $doctorReplyVoteEntity->setPractoAccountId($practoAccountId);
                $doctorReplyVoteEntity->setReply($doctorReplyEntity);
                $doctorReplyVoteEntity->setVote($vote);
                $doctorReplyEntity->addVote($doctorReplyVoteEntity);
                $changed = true;
            } else {
                $doctorReplyVoteEntity->setVote($vote);
                $this->helper->persist($doctorReplyVoteEntity);
                $changed = true;
            }
        }

        if (array_key_exists('flag', $doctorReply) && Utility::toBool($doctorReply['flag'])) {
            $er = $this->helper->getRepository(ConsultConstants::DOCTOR_REPLY_FLAG_ENTITY_NAME);
            $flag = $er->findOneBy(array('doctorReply' => $doctorReplyEntity, 'practoAccountId' => $_SESSION['authenticated_user']['id'], 'softDeleted' => 0));
            if (!empty($flag)) {
                @$error['error'] = 'The user has already flagged this comment';
                throw new ValidationError($error);
            }

            $flag = new DoctorReplyFlag();
            $flag->setDoctorReply($doctorReplyEntity);
            if (array_key_exists('flag_code', $doctorReply) && !empty($doctorReply['flag_code'])) {
                $flag->setFlagCode(trim($doctorReply['flag_code']));
            } else {
                @$error['error'] = 'flag_code is mandatory';
                throw new ValidationError($error);
            }

            if (array_key_exists('flag_text', $doctorReply) && !empty($doctorReply['flag_text'])) {
                if ($doctorReply['flag_code'] != 'OTH') {
                    @$error['error'] = 'Flag Text is required only for code Other';
                    throw new ValidationError($error);
                }

                $flag->setFlagText($doctorReply['flag_text']);
            } else {
                if ($doctorReply['flag_code'] === 'OTH') {
                    @$error['error'] = 'flag_text is mandatory for code OTH';
                    throw new ValidationError($error);
                }
            }

            $flag->setPractoAccountId($practoAccountId);
            // $this->validate($flag);
            $this->helper->persist($flag);
            $changed = true;

            //return $flag;
        } elseif (array_key_exists('flag', $doctorReply) && !Utility::toBool($doctorReply['flag'])) {
            $er = $this->helper->getRepository(ConsultConstants::DOCTOR_REPLY_FLAG_ENTITY_NAME);
            $flag = $er->findOneBy(array('doctorReply' => $doctorReplyEntity, 'practoAccountId' => $_SESSION['authenticated_user']['id'], 'softDeleted' => 0));
            if (empty($flag)) {
                @$error['error'] = 'The user has not flagged the reply';
                throw new ValidationError($error);
            }

            $flag->setBoolean('softDeleted', true);
            $this->helper->persist($flag);
            $changed = true;

        } elseif (array_key_exists('flag', $doctorReply)) {
            @$error['error'] = 'Not a correct value for flag';
            throw new ValidationError($error);
        }


        if ($changed) {
            $this->validate($doctorReplyEntity);
            $this->helper->persist($doctorReplyEntity, true);
        }

        return $this->getReplyById($id, $practoAccountId);



    }

    /**
     * @param int $id
     * @param int $practoAccountId
     *
     * @return \ConsultBundle\Response\ReplyResponseObject
     * @throws \HttpException
     */
    public function getReplyById($id, $practoAccountId = 0)
    {
        if (empty($id)) {
            return null;
        }

        if (empty($practoAccountId)) {
            $practoAccountId = $_SESSION['authenticated_user']['id'];
        }
        $reply =  new ReplyResponseObject();
        /**
         * @var DoctorQuestionRepository $er
         */
        $er = $this->helper->getRepository(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME);
        $replyArray = $er->findReplyById($id, $practoAccountId);
        if (count($replyArray) == 0 || count($replyArray) > 1) {
            throw new HttpException("Invalid Reply Id", Codes::HTTP_BAD_REQUEST);
        }

        $reply->setAttributes($replyArray[0]);
        $reply->setDoctorFromAttributes($replyArray[0]);

        return $reply;
    }
}
