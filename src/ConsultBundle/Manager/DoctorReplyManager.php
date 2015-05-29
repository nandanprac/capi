<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 18:04
 */

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\DoctorQuestion;
use ConsultBundle\Entity\DoctorReply;
use ConsultBundle\Entity\DoctorReplyRating;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Util\Codes;
use ConsultBundle\Queue\AbstractQueue as Queue;

class DoctorReplyManager extends BaseManager
{
    public static $mandatoryFields;
    public function __construct(Queue $queue)
    {
        if (!isset(self::$mandatoryFields)) {
            self::$mandatoryFields = new ArrayCollection();
            self::$mandatoryFields->add("doctor_question_id");
            self::$mandatoryFields->add("id");
        }
        $this->queue = $queue;
    }

    /**
     * @param $doctorQuestionId
     * @param $practoAccountId
     * @param $answerText
     * @return DoctorReply
     * @throws \HttpException
     */
    public function replyToAQuestion($doctorQuestionId, $practoAccountId, $answerText)
    {
        $doctorReply = new DoctorReply();
        $doctorQuestion = $this->helper->loadById(
            $doctorQuestionId,
            ConsultConstants::$DOCTOR_QUESTION_ENTITY_NAME);

        if (is_null($doctorQuestion->getQuestion())) {
           throw new \HttpException ("Error:Doctor has not been assigned the question", Codes::HTTP_BAD_REQUEST);
        }

        if($practoAccountId != $doctorQuestion->getPractoAccountId()) {
            throw new \HttpException("You are not allowed to perform this operation", Codes::HTTP_FORBIDDEN);
        }

        if($doctorQuestion->getRejectedAt()) {
              throw new \HttpException("The question has been rejected", Codes::HTTP_BAD_REQUEST);
        }

        if($doctorQuestion->getState() != "UNANSWERED") {
          throw new \HttpException("The doctor is not allowed to answer this question", Codes::HTTP_BAD_REQUEST);
        }

        $doctorQuestion->setState("ANSWERED");
        $doctorQuestion->getQuestion()->setState("ANSWERED");
        $doctorReply->setDoctorQuestion($doctorQuestion);
        $doctorReply->setText($answerText);

/*
      try {
          $this->validate($doctorReply);

      }catch(\Exception $e)
      {
          //To be implemented
          throw new Exception($e, $e->getMessage());
      }*/
      $this->queue->setQueueName(Queue::CONSULT_GCM)->sendMessage(json_encode(array("type"=>"query_answered", "message"=>"Your Query has been answered", "id"=>$doctorQuestion->getQuestion()->getId(), "user_ids"=>array($doctorQuestion->getQuestion()->getPractoAccountId()))));
      $this->helper->persist($doctorReply, true);

      return $doctorReply->getDoctorQuestion()->getQuestion();
    }

    /**
     * @param $postData
     * @return mixed
     * @throws \HttpException
     */
    public function patchDoctorReply($postData)
    {


        $practoAccountId = $postData['practo_account_id'];

        if(array_key_exists("doctor_reply", $postData)) {

            $doctorReply = $postData['doctor_reply'];
        } else {
            throw new \HttpException("doctor_reply is mandatory", Codes::HTTP_BAD_REQUEST);
        }

        $changed = false;

        $this->helper->checkForMandatoryFields(self::$mandatoryFields, $doctorReply);

        //Fetch Doctor Reply
        $id = $doctorReply['id'];

        $doctorReplyEntity = $this->helper->loadById($id, ConsultConstants::$DOCTOR_REPLY_ENTITY_NAME);
        if(empty($doctorReplyRatingEntity))
        {
            throw new \HttpException("Not a valid Doctor Reply Id", Codes::HTTP_BAD_REQUEST );
        }

        $ownerId = $doctorReplyEntity->getDoctorQuestion()->getQuestion()->getPractoAccountId();

        //Mark As Best Answer
        if (array_key_exists("selected", $doctorReply)) {
            if ($ownerId != $practoAccountId) {
                throw new \HttpException("Only the one who has asked the question can mark it as best answer", Codes::HTTP_BAD_REQUEST);
            }

            if (!$doctorReplyEntity->isSelected()) {
                $doctorReplyEntity->setSelected(true);
                $changed = true;
            }
        }

        //Mark the answer as viewed
        if (array_key_exists("viewed", $doctorReply)) {
            if ($ownerId != $practoAccountId) {
                throw new \HttpException("Not the owner of the question", Codes::HTTP_BAD_REQUEST);
            }

            if (!$doctorReplyEntity->getViewedAt())
            {
                $doctorReplyEntity->setViewedAt(new \DateTime());
                $changed = true;
            }
        }

        if(array_key_exists("like", $doctorReply))
        {
            $like = $doctorReply['like'];
            //$er = new EntityRepository();
            $er = $this->helper->getRepository(ConsultConstants::$DOCTOR_REPLY_RATING_ENTITY_NAME);



         $doctorReplyRatingEntity = $er->findOneBy(array("practoAccountId" => $practoAccountId,
                "doctorReply" => $doctorReplyEntity,

         ));

           //var_dump($doctorReplyRatingEntity->getId());die;



            //Like
            if(!$doctorReplyRatingEntity && $like)
            {
                $doctorReplyRatingEntity = new DoctorReplyRating();
                $doctorReplyRatingEntity->setPractoAccountId($practoAccountId);
                $doctorReplyRatingEntity->setDoctorReply($doctorReplyEntity);
                $doctorReplyEntity->addRating($doctorReplyRatingEntity);
                $changed = true;
            }



            //Unlike
            if($doctorReplyRatingEntity && !$like)
            {
                $changed = true;
                $this->helper->remove($doctorReplyRatingEntity);

            }

        }

        if($changed)
        {
            $this->helper->persist($doctorReplyEntity, true);
        }


        return $doctorReplyEntity;


    }




}
