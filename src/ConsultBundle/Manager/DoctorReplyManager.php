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
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Util\Codes;

class DoctorReplyManager extends BaseManager {


    public static $mandatoryFields = ["doctor_question_id", "id"];


    /**
     * @param $doctorQuestionId
     * @param $practoAccntId
     * @param $answerText
     * @return DoctorReply|string
     */
    public function replyToAQuestion($doctorQuestionId, $practoAccntId, $answerText)
  {
      $doctorReply = new DoctorReply();

      $doctorQuestion = $this->helper->loadById($doctorQuestionId, ConsultConstants::$DOCTOR_QUESTION_ENTITY_NAME);

      //$doctorQuestion = new DoctorQuestion();
      if(is_null($doctorQuestion))
      {
         throw new \HttpException ("Error:Doctor has not been assigned the question", Codes::HTTP_BAD_REQUEST);
      }

      if($practoAccntId != $doctorQuestion->getPractoAccountId())
      {
          throw new \HttpException("You are not allowed to perform this operation", Codes::HTTP_FORBIDDEN);
      }

      if($doctorQuestion->getRejectedAt())
      {
          throw new \HttpException("The question has been rejected", Codes::HTTP_BAD_REQUEST);
      }


      if($doctorQuestion->getState() != "ASSIGNED")
      {
          throw new \HttpException("The doctor is not allowed to answer this question". Codes::HTTP_BAD_REQUEST);
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

      $this->helper->persist($doctorReply, true);

      return $doctorReply;


  }

    /**
     * @param $postData
     * @throws \HttpException
     */
    public function patchDoctorReply($postData)
    {
        $practoAccountId = $postData['practo_account_id'];
        $doctorReply = $postData['doctor_reply'];
        $changed = false;

        $this->helper->checkForMandatoryFields(DoctorReply::mandatoryFields, $doctorReply);

        //Fetch Doctor Reply
        $id = $doctorReply['id'];

        $doctorReplyEntity = $this->helper->loadById($id, ConsultConstants::$DOCTOR_REPLY_ENTITY_NAME);


        $ownerId = $doctorReplyEntity->getDoctorQuestion()->getQuestion()->getPractoAccountId();

        //Mark As Best Answer
        if(array_key_exists("selected", $doctorReply))
        {
            if($ownerId != $practoAccountId)
            {
                throw new \HttpException("Only the one who has asked the question can mark it as best answer", Codes::HTTP_BAD_REQUEST);
            }
            if(!$doctorReplyEntity->isSelected())
            {
                $doctorReplyEntity->setSelected(true);
                $changed = true;
            }


        }

        //Mark the answer as viewed
        if(array_key_exists("viewed_at", $doctorReply))
        {
            if($ownerId != $practoAccountId)
            {
                throw new \HttpException("Not the owner of the question", Codes::HTTP_BAD_REQUEST);
            }
            if(!$doctorReplyEntity->getViewedAt())
            {
                $doctorReplyEntity->setViewedAt($doctorReply['viewed_at']);
                $changed = true;
            }
        }

        //mark that the answer has been liked/unliked by the user
        if(array_key_exists("like", $doctorReply))
        {
            $like = $doctorReply['like'];

            $er = $this->helper->getRepository(ConsultConstants::$DOCTOR_REPLY_RATING_ENTITY_NAME);


            $doctorReplyRatingEntity = $er->findBy(["practoAccountId" => $practoAccountId,
                "reply" => $doctorReplyEntity,
                "softDeleted" => 0
        ]);

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

                $doctorReplyRatingEntity->setSoftDeleted(true);
                $changed = true;

            }

        }

        if($changed)
        {
            $this->helper->persist($doctorReplyEntity, true);

        }


    }




}
