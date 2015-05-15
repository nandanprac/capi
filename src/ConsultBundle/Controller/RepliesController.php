<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 12:43
 */

namespace ConsultBundle\Controller;


use ConsultBundle\Entity\DoctorReply;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

class RepliesController extends FOSRestController

{

    /**
     * @param Request $request
     * @return DoctorReply
     *
     * @View()
     */
     public function postDoctorReplyAction(Request $request)
     {
         $answerText = $request->request->get("text");
         $practoAccountId  = $request->request->get("practo_account_id");
         $doctorQuestionId = $request->request->get("doctor_question_id");
         //$doctorReply  = new DoctorReply();
         //$doctorReply->setText($answerText);
         //return $doctorReply;
         $doctorReplyManager = $this->get('consult.doctorReplyManager');
         $doctorReply = $doctorReplyManager->replyToAQuestion($doctorQuestionId, $practoAccountId, $answerText);

         return $doctorReply;
     }

    /**
     * @param $replyId
     * @param $practoAccntId
     *
     *
     * @View()
     */
    public function postMarkAsBestAnswerAction($replyId, $practoAccntId)
    {
        //TODO
    }

    /**
     * @param $replyId
     * @param $rating
     * @param $practoAccntId
     *
     * @View()
     */
    public function postRateAReplyAction($replyId, $rating, $practoAccntId)
    {
        //TODO
    }

    /**
     * @param $replyId
     *
     * @View()
     */
    public function getReplyAction($practoAccntId, $doctorId, $replyId)
    {
        //TODO
    }
}