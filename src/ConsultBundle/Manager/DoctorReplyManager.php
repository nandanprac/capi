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

class DoctorReplyManager extends BaseManager
{

    /**
     * @param $doctorQuestionId
     * @param $practoAccntId
     * @param $answerText
     */
    public function replyToAQuestion($doctorQuestionId, $practoAccntId, $answerText)
    {
        $doctorQuestion = $this->helper->loadById($doctorQuestionId, ConsultConstants::$DOCTOR_QUESTION_ENTITY_NAME);
        if (is_null($doctorQuestion)) {
            return "Error:Doctor has not been assigned the question";
        }
        $doctorReply = new DoctorReply();
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
}