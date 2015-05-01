<?php

namespace ConsultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ConsultBundle\Manager\ValidationError;

/**
 * Questions Controller
 *
 */
class QuestionsController extends Controller
{
    /**
     * Create Question
     *
     * @return View
     */
    public function postQuestionAction()
    {
        $postData = $this->getRequest()->request->all();
        $questionManager = $this->get('consult.question_manager');

        try {
            $question = $questionManager->add($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        //$router = $this->get('router');
        //$patientGrowthURL = $router->generate('get_patientgrowth', array(
        //    'patientGrowthId' => $patientGrowth->getId()), true);

        return View::create(
            $question,
            Codes::HTTP_CREATED);
    }
}
