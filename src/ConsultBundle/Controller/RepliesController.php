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
         $doctorReplyManager = $this->get('consult.doctorReplyManager');
         $doctorReply = $doctorReplyManager->replyToAQuestion($doctorQuestionId, $practoAccountId, $answerText);

         return $doctorReply;
     }

   /**
    *
     *
     *
     * @View()
     */
    public function patchDoctorReplyAction(Request $request)
    {
        $postData = $request->request->all();
        $doctorReplyManager = $this->get('consult.doctorReplyManager');
        try{
            $doctorReply = $doctorReplyManager->patchDoctorReply($postData);
        }catch (\HttpException $e)
        {
            return View::create(json_decode($e->getMessage(),true), $e->getCode());
        }


        return array("doctor_reply"=> $doctorReply);
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