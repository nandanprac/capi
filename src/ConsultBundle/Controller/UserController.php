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

/**
 * Controller for User's profile info updation
 */
class UserController extends FOSRestController
{
    /**
     * @return View
     */
    public function postUserInfoAction()
    {
        $requestParams = $this->getRequest()->request->all();
        $profileToken = $this->getRequest()->headers->get('X-Profile-Token');
        $userManager = $this->get('consult.user_manager');

        try {
            $userConsultEntry = $userManager->add($requestParams, $profileToken);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            $userConsultEntry,
            Codes::HTTP_CREATED
        );
    }

    /**
     * Load additional info of a User
     *
     * @return View
     */
    public function getUserInfoAction()
    {
        $userManager = $this->get('consult.user_manager');
        $requestParams = $this->getRequest()->query->all();

        try {
            $userProfiles = $userManager->load($requestParams);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        } catch (ValidationError $e) {
            return View::create($e->getMessage(), Codes::HTTP_BAD_REQUEST);
        }

        if (empty($userProfiles)) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return $userProfiles;
    }
}
