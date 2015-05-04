<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 18:04
 */

namespace ConsultBundle\Manager;




use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\DoctorReply;

class DoctorReplyManager extends BaseManager {

  public function replyToAQuestion($doctorId, $questionId, $practoAccntId, $answerText)
  {
      $doctorReply = new DoctorReply();

      $doctorQuestionRepository = $this->helper->getRepository(ConsultConstants::$DOCTOR_QUESTION_ENTITY_NAME);

      //Needs to be implemented
      $doctorQuestion = $doctorQuestionRepository->loadByDoctorAndQuestion($doctorId, $questionId);

      $doctorReply.setDoctorQuestion($doctorQuestion);

      $this->validate($doctorReply);

      $this->helper->persist($doctorReply, "true");


  }

}
