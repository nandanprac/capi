<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 12:43
 */

namespace ConsultBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;

class RepliesController extends FOSRestController

{
    /**
     * @param $doctorId
     * @param $questionId
     * @param $practoAccntId
     *
     * @View()
     */
     public function postDoctorReplyAction($doctorId, $questionId, $practoAccntId)
     {
         //TODO
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