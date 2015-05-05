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

class RepliesController extends FOSRestController

{
    /**
     * @param $doctorId
     * @param $questionId
     * @param $practoAccntId
     * @param $answerText
     *
     * @View()
     */
     public function postDoctorReplyAction($doctorQuestionId,  $practoAccntId, $answerText)
     {
         $doctorReply  = new DoctorReply();
         $doctorReply->setText($answerText);
         return $doctorReply;
         //$doctorReplyManager = $this->get('consult.doctorReplyManager');
         //$doctorReplyManager->replyToAQuestion($doctorQuestionId, $practoAccntId, $answerText);
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