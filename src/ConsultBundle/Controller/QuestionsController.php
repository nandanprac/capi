<?php

namespace ConsultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\Rest\Util\Codes;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        //$router = $this->get('router');
        //$patientGrowthURL = $router->generate('get_patientgrowth', array(
        //    'patientGrowthId' => $patientGrowth->getId()), true);

        return View::create($question,
            Codes::HTTP_CREATED);
    }
}
