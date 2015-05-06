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
class KeepController extends Controller
{
    /**
     * Create Question
     *
     * @return View
     */
    public function postKeepAction()
    {
        $postData = $this->getRequest()->request->all();
        $keepManager = $this->get('consult.keep_manager');

        try {
            $keep = $keepManager->add($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create($keep, Codes::HTTP_CREATED);
    }

    public function getKeepsAction()
    {
        $keepManager = $this->get('consult.keep_manager');

        try {
            $keep = $keepManager->load();
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }
        if (null === $keep) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return array("keep" => $keep);
    }
}
