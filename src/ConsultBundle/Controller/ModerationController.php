<?php

namespace ConsultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ConsultBundle\Manager\ValidationError;
use ConsultBundle\Response\DetailQuestionResponseObject;
use ConsultBundle\Mapper\QuestionMapper;

class ModerationController extends BaseConsultController
{
    /**
     * @return array Summary counts
     */

    public function getSummaryAction()
    {
        $moderationManager = $this->get('consult.moderation_manager');
        $summaryList = $moderationManager->loadSummary();
        return $summaryList;
    }

    /**
     * @return int customCount
     */

    public function getCustomDateAction(Request $request)
    {
        $moderationManager= $this->get('consult.moderation_manager');
        $dates = $request->query->all();

        $customCount = $moderationManager->loadCustomCount($dates);
        return $customCount;

    }

    /**
     * @param $questionID
     * @return ConsultBundle\Response\DetailQuestionResponseObject
     */


    public function getModerationAction($questionID, Request $request)
    {
        $logger = $this->get('logger');
        $logger->info("Get Question".$questionID);

        $moderationManager = $this->get('consult.moderation_manager');
        $question=$moderationManager->load($questionID);

        $response = new Response();
        $quesArr=QuestionMapper::mapToModerationArray($question);
        //var_dump(json_encode($quesArr, true));die;
        $response->setContent(json_encode($quesArr));
        $response->headers->set("access-control-allow-origin","*");
        return $response;
    }


    /**
     * @param Request $requestRec - request Object
     * @return array Question - list of question object

     */

    public function getModerationsAction(Request $requestRec)
    {
        $moderationManager = $this->get('consult.moderation_manager');
        $request = $requestRec->query->all();



        //FILTERS WILL GO HERE AND GET PUSHED INTO THE ARRAY

        try {
            $questionList = $moderationManager->loadByFilters($request);

        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $questionList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        $response = new Response();
        $response->setContent(json_encode($questionList));
        $response->headers->set("access-control-allow-origin","*");
        return $response;


    }


    /**
     * @param int $questionId - questionid for change
     * @return Response
     */


    public function getStateAcceptAction($questionId)
    {

        $response = new Response();
        $response->headers->set("access-control-allow-origin","*");
        $response->setContent(json_encode(array("state"=>"ACCEPTED")));
        $moderationManager = $this->get('consult.moderation_manager');
        try {
            $state="ACCEPT";
            $questionFinal = $moderationManager->changeState(array('question_id'=>$questionId,'state'=>$state));
        } catch (ValidationError $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);

        }
        return $response;
    }

    /**
     * @param int $questionId - questionid for change
     * @return null
     */

    public function getStateRejectAction($questionId)
    {

        $moderationManager = $this->get('consult.moderation_manager');
        try {
            $state="REJECT";
            $questionFinal = $moderationManager->changeState(array('question_id'=>$questionId,'state'=>$state));
        } catch (ValidationError $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);

        }
        $response = new Response();
        $response->headers->set("access-control-allow-origin","*");
        $response->setContent(json_encode(array("state"=>"REJECTED")));
        return $response;
    }



    public function getFlagDeleteAction($commentID)
    {
        $moderationManager = $this->get('consult.moderation_manager');
        try{
            $moderationManager->softDeleteComment($commentID);
        } catch (ValidationError $e)
        {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }
        $response = new Response();
        $response->headers->set("access-control-allow-origin","*");
        $response->setContent(json_encode(array("result"=>"Soft Deleted Comment")));
        return $response;
    }


}