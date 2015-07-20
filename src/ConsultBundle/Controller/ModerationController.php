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

/**
 * Dashboard API controller
 */
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
     * @param Request $request
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
     * @param integer $questionID
     * @param Request $request
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
        $response->setContent(json_encode($quesArr));
        $response->headers->set("access-control-allow-origin", "*");

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
        $response = new Response();
        $response->headers->set("access-control-allow-origin", "*");

        //FILTERS WILL GO HERE AND GET PUSHED INTO THE ARRAY

        try {
            $questionList = $moderationManager->loadByFilters($request);

        } catch (AccessDeniedException $e) {
            $response->setContent(json_encode($e->getMessage()));

            return $response;
        }

        if (null === $questionList) {
            $response->setContent(json_encode(array('error'=>Codes::HTTP_NOT_FOUND)));

            return $response;
        }

        $response->setContent(json_encode($questionList));

        return $response;
    }


    /**
     * @param int $questionId - questionid for change
     * @return Response
     */
    public function getStateAcceptAction($questionId)
    {
        $response = new Response();
        $response->headers->set("access-control-allow-origin", "*");
        $moderationManager = $this->get('consult.moderation_manager');
        try {
            $state = "ACCEPT";
            $questionFinal = $moderationManager->changeState(array('question_id' => $questionId, 'state' => $state));
        } catch (ValidationError $e) {
            $response->setContent(json_encode($e->getMessage()));

            return $response;
        }
        $response->setContent(json_encode(array("state"=>"ACCEPTED")));

        return $response;
    }

    /**
     * @param int $questionId - questionid for change
     * @return null
     */
    public function getStateRejectAction($questionId)
    {
        $moderationManager = $this->get('consult.moderation_manager');
        $response = new Response();
        $response->headers->set("access-control-allow-origin", "*");
        try {
            $state="REJECT";
            $questionFinal = $moderationManager->changeState(array('question_id' => $questionId, 'state' => $state));
        } catch (ValidationError $e) {
            $response->setContent(json_encode($e->getMessage()));

            return $response;

        }
        $response->setContent(json_encode(array("state"=>"REJECTED")));

        return $response;
    }

    /**
     * @param integer $commentID
     * @return string
     */
    public function getCommentDeleteAction($commentID)
    {
        $response = new Response();
        $response->headers->set("access-control-allow-origin", "*");
        $moderationManager = $this->get('consult.moderation_manager');
        try {
            $moderationManager->softDeleteComment($commentID);
        } catch (ValidationError $e) {
            $response->setContent(json_encode($e->getMessage()));

            return $response;
        }

        $response->setContent(json_encode(array("result"=>"Soft Deleted Comment")));

        return $response;
    }

    /**
     * @param integer $flagID
     * @return string
     */
    public function getFlagDeleteAction($flagID)
    {
        $response = new Response();
        $response->headers->set("access-control-allow-origin", "*");
        $moderationManager = $this->get('consult.moderation_manager');
        try {
            $moderationManager->softDeleteFlag($flagID);
        } catch (ValidationError $e) {
            $response->setContent(json_encode($e->getMessage()));

            return $response;
        }
        $response->setContent(json_encode(array("result"=>"Soft Deleted Flag")));

        return $response;
    }

    /**
     * @param Request $requestRec
     * @return array
     */
    public function getDoctorDetailsAction(Request $requestRec)
    {
        $request = $requestRec->query->all();
        $response = new Response();
        $response->headers->set("access-control-allow-origin", "*");
        $moderationManager = $this->get('consult.moderation_manager');
        try {
            $docDetails = $moderationManager->getDoctorDetails($request);
        } catch (ValidationError $e) {
            $response->setContent(json_encode($e->getMessage()));

            return $response;
        }
        $response->setContent(json_encode(array("doctor_details"=>$docDetails)));

        return $response;
    }
}
