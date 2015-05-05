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
use Symfony\Component\Config\Definition\Exception\Exception;

class DoctorReplyManager extends BaseManager {

    /**
     * @param $doctorQuestionId
     * @param $practoAccntId
     * @param $answerText
     */
    public function replyToAQuestion($doctorQuestionId, $practoAccntId, $answerText)
  {
      $doctorReply = new DoctorReply();

      $doctorQuestion = $this->helper->findById($doctorQuestionId, ConsultConstants::$DOCTOR_QUESTION_ENTITY_NAME);

      $doctorReply.setDoctorQuestion($doctorQuestion);


      try {
          $this->validate($doctorReply);

      }catch(Exception $e)
      {
          //To be implemented
          throw new Exception($e, $e->getMessage());
      }

      $this->helper->persist($doctorReply, "true");


  }

}
