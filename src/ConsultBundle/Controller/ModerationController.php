<?php

namespace ConsultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ConsultBundle\Manager\ValidationError;

class ModerationController extends Controller
{


        /**
     * @param Request $requestRec - request Object
     * @return array Question - list of question objects
     */
    public function getModerationsAction(Request $requestRec)
    {
        $moderationManager = $this->get('consult.moderation_manager');
        $request = $requestRec->query->all();

        try {
            $questionList = $moderationManager->loadByFilters($request);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $questionList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        $abc=array('a'=>'1','b'=>'2');

        return $this->render(
            'ConsultBundle:Moderation:index.html.twig'
            ,$abc);


    }

    /**
     * @param int $questionId - questionid for change
     * @return \FOS\RestBundle\View\View
     */


    public function changeStateAcceptAction($questionId)
    {

        $moderationManager = $this->get('consult.moderation_manager');
        try {
            $state="ACCEPT";
            $questionFinal = $moderationManager->changeState(array('question_id'=>$questionId,'state'=>$state));
        } catch (ValidationError $e) {
            return $this->render('ConsultBundle:Moderation:change.html.twig',json_decode($e->getMessage(), true));

        }

        return $this->render('ConsultBundle:Moderation:change.html.twig',array("question" => $questionFinal));


    }

    /**
     * @param int $questionId - questionid for change
     * @return \FOS\RestBundle\View\View
     */

    public function changeStateRejectAction($questionId)
    {

        $moderationManager = $this->get('consult.moderation_manager');
        try {
            $state="REJECT";
            $questionFinal = $moderationManager->changeState(array('question_id'=>$questionId,'state'=>$state));
        } catch (ValidationError $e) {
            return $this->render('ConsultBundle:Moderation:change.html.twig',json_decode($e->getMessage(), true));

        }

        var_dump($questionFinal);
        return $this->render('ConsultBundle:Moderation:change.html.twig',array("question" => $questionFinal));


    }


}