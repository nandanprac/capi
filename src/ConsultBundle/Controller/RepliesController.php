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
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as Views;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

class RepliesController extends FOSRestController

{

    /**
     * @param Request $request
     * @return DoctorReply
     *
     * @View()
     *
     */
     public function postDoctorReplyAction(Request $request)
     {

         $answerText = $request->request->get("text");
         $practoAccountId  = $request->request->get("practo_account_id");
         $doctorQuestionId = $request->request->get("doctor_question_id");
         $doctorReplyManager = $this->get('consult.doctorReplyManager');
         try{
             $doctorReply = $doctorReplyManager->replyToAQuestion($doctorQuestionId, $practoAccountId, $answerText);

         }catch(\HttpException $e)
         {
             //var_dump($e->getCode());die;
             return Views::create($e->getMessage(), $e->getCode());
         }

         return $doctorReply;
     }

    /**
     * @param Request $request
     * @return array|Views
     *
     * @View()
     */
    public function patchDoctorReplyAction(Request $request)
    {
        $postData = $request->request->all();
        //var_dump($postData);die;
        $doctorReplyManager = $this->get('consult.doctorReplyManager');
        try{
            $doctorReply = $doctorReplyManager->patchDoctorReply($postData);
        }catch (\HttpException $e)
        {
            return Views::create($e->getMessage(), $e->getCode());
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
     * @param $practoAccntId
     *
     * @View()
     */
    public function getReplyAction($practoAccntId)
    {
      $m = $this->get('consult.retrieve_user_profile_util');
       $user =  $m->retrieveUserProfileNew($practoAccntId);

        var_dump($user);die;
    }


}
