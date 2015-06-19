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

/**
 * Class SecurityListener
 * @package ConsultBundle\EventListener
 */
class SecurityListener
{
    private $authenticationUtils;


    /**
     * @param AuthenticationUtils $authenticationUtils
     */
    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     *
     * @return bool
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $request->getSession()->all();
        $_SESSION['validated'] = false;
        $logger = $this->get('logger');
        $logger->info($event->getRequestType()." ".$request->getHost()." ".$request);

        $profileToken = $request->headers->get('X-PROFILE-TOKEN');
        $practoAccountId = $request->get("practo_account_id");

        if (empty($practoAccountId)) {
            $practoAccountId = $request->query->get('practo_account_id');
        }



        if (is_null($profileToken) || is_null($practoAccountId)) {
            $_SESSION['validated'] = false;

            return false;

        }

        try {
            $this->authenticationUtils
                ->authenticateWithAccounts($practoAccountId, $profileToken);
        } catch (\Exception $e) {
            $responseRet = new Response();
            $responseRet->setStatusCode(Response::HTTP_FORBIDDEN);
            $responseRet->setContent("Unauthorised Access");
            $event->setResponse($responseRet);
        }


    }
}
