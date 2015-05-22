<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 18/05/15
 * Time: 11:53
 */

namespace ConsultBundle\EventListener;

use ConsultBundle\Utility\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SecurityListener {

    private $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        //var_dump("123");


        if ($request->getMethod() === 'GET') {
            //var_dump("GET");

            return;
        }

        $session = $request->getSession();

        if ($session->get("isValidated") === true) {

            return;
        }


        $profileToken = $request->headers->get('X-PROFILE-TOKEN');
        $practoAccountID = $request->get("practo_account_id");
        //var_dump($practoAccountID);die;
        //var_dump(is_null($profileToken||$practoAccountID));
        if (is_null($profileToken) || is_null($practoAccountID)) {
            $responseRet = new Response();
            $responseRet->setStatusCode(Response::HTTP_FORBIDDEN);
            $event->setResponse($responseRet);

        }
        try{
            $this->authenticationUtils->authenticateWithAccounts($practoAccountID, $profileToken);
        }catch(\Exception $e)
        {
            $responseRet = new Response();
            $responseRet->setStatusCode(Response::HTTP_FORBIDDEN);
            $event->setResponse($responseRet);
        }






    }

















}