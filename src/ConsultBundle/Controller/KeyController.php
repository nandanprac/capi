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
class KeyController extends Controller
{
    /**
     * Create Question
     *
     * @return View
     */
    public function postKeyAction()
    {
        $postData = $this->getRequest()->request->all();
        $keyManager = $this->get('consult.key_manager');

        try {
            $key = $keyManager->add($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create($key, Codes::HTTP_CREATED);
    }
}
