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
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

       /* if ($request->getMethod() === 'GET') {
            return;
        }*/


        if (array_key_exists("authenticated_user", $_SESSION)) {
            return;
        }


        $profileToken = $request->headers->get('X-PROFILE-TOKEN');
        $practoAccountID = $request->get("practo_account_id");
        if (empty($practoAccountID)) {
            $practoAccountID = $request->query->get('practo_account_id');
        }

        if (is_null($profileToken) || is_null($practoAccountID)) {
            $responseRet = new Response();
            $responseRet->setStatusCode(Response::HTTP_FORBIDDEN);
            $event->setResponse($responseRet);

        }

        try {
            $this->authenticationUtils
                ->authenticateWithAccounts($practoAccountID, $profileToken);
        } catch (\Exception $e) {
            $responseRet = new Response();
            $responseRet->setStatusCode(Response::HTTP_FORBIDDEN);
            $event->setResponse($responseRet);
        }
    }
}
