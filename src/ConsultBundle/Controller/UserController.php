<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 12:43
 */

namespace ConsultBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ConsultBundle\Manager\ValidationError;

class UserController extends FOSRestController{

    /**
     * Additional info of user addition
     *
     */
    public function postUserConsultinfoAction()
    {
        $postData = $this->getRequest()->request->all();
        $userManager = $this->get('consult.user_manager');

        try {
            $userConsultEntry = $userManager->add($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(),true), Codes::HTTP_BAD_REQUEST);
        }
        return View::create(
            $userConsultEntry,
            Codes::HTTP_CREATED);
    }

    /**
     * Load additional info of a User
     *
     * @param interger $practoId
     *
     * @return View
     */
    public function getUserConsultinfoAction($practoId)
    {
        $userManager = $this->get('consult.user_manager');
        try {
            $userConsultEntry = $userManager->load($practoId);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }
        if (null === $userConsultEntry) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        } else if ($userConsultEntry->isSoftDeleted()) {
            return View::create(null, Codes::HTTP_GONE);
        }

        return $userConsultEntry;

    }

}
