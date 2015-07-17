<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 18/05/15
 * Time: 11:53
 */

namespace ConsultBundle\EventListener;

use ConsultBundle\Utility\AuthenticationUtils;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use FOS\RestBundle\Util\Codes;

/**
 * Class SecurityListener
 * @package ConsultBundle\EventListener
 */
class SecurityListener
{
    private $authenticationUtils;
    private $logger;


    /**
     * @param \ConsultBundle\Utility\AuthenticationUtils $authenticationUtils
     * @param \Symfony\Bridge\Monolog\Logger             $logger
     */
    public function __construct(AuthenticationUtils $authenticationUtils, Logger $logger)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->logger = $logger;
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

        $this->logger->info($event->getRequestType()." ".$request->getHost()." ".$request);

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
            return $this->authenticationUtils
                ->authenticateWithAccounts($practoAccountId, $profileToken);
        } catch (\Exception $e) {
            $responseRet = new Response();
            $responseRet->setStatusCode(Response::HTTP_FORBIDDEN);
            $responseRet->setContent(json_encode(array('code' =>Codes::HTTP_FORBIDDEN, 'message' => "Unauthorised Access")));
            $event->setResponse($responseRet);
        }
    }
}