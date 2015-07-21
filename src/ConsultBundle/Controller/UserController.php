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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ConsultBundle\Manager\ValidationError;

/**
 * Controller for User's profile info updation
 */
class UserController extends BaseConsultController
{
    /**
     * @param Request $request
     * @return View
     */
    public function postUserInfoAction(Request $request)
    {
        $requestParams = $request->request->all();
        $profileToken = $request->headers->get('X-Profile-Token');
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
     * @param  Request $request
     * @return View
     */
    public function getUserInfoAction(Request $request)
    {
        $userManager = $this->get('consult.user_manager');
        $requestParams = $request->query->all();

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

    /**
     * @param Request $request
     * @return View
     */
    public function getUserConsentAction(Request $request)
    {
        $practoAccountId = $this->authenticate(true);
        $userManager = $this->get('consult.user_manager');

        $userConsent = $userManager->checkConsultEnabled($practoAccountId);
        return View::create(array("consent" => $userConsent));
    }

    /**
     * @param Request $request
     * @return View
     */
    public function postUserConsentAction(Request $request)
    {
        $practoAccountId = $this->authenticate(true);
        $userManager = $this->get('consult.user_manager');

        $userConsent = $userManager->setConsultEnabled($practoAccountId);
        return View::create(array("consent" => $userConsent));
    }
}
