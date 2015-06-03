<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 18/05/15
 * Time: 11:23
 */

namespace ConsultBundle\Utility;

use GuzzleHttp\Client;
use PhpCollection\Map;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AuthenticationUtils
 *
 * @package ConsultBundle\Utility
 */
class AuthenticationUtils
{


    private static $authenticationMap;

    private $accountHost;


    /**
     * @param $accountHost
     */
    public function __construct($accountHost)
    {
        $this->accountHost = $accountHost;

        if (AuthenticationUtils::$authenticationMap === null) {
            AuthenticationUtils::$authenticationMap = new Map();

        }


    }

    /**
     * @param $practoAccountId
     * @param $profileToken
     *
     * @return bool
     */
    public function authenticateWithAccounts($practoAccountId, $profileToken)
    {

        if ($this->isAlreadyValidated($practoAccountId, $profileToken)) {
            return true;
        }

        $this->validateWithTokenNew($practoAccountId, $profileToken);
    }

    /**
     * @param $practoAccountId
     * @param $profileToken
     * @return bool
     */
    private function isAlreadyValidated($practoAccountId, $profileToken)
    {
        return ($profileToken === AuthenticationUtils::$authenticationMap->get($practoAccountId));
    }



    private function validateWithTokenNew($practoAccountId, $profileToken)
    {

            $client = new Client(
                ['base_url' => $this->accountHost,
                'defaults' => ['headers' => ['X-Profile-Token' => $profileToken]]]
            );
            $res = $client->get('/get_profile_with_token');


            $userJson = $res->json();

            $userId = $userJson["id"];


            $code = $res->getStatusCode();

            if (is_null($userId) || $userId != $practoAccountId || $code[0] > 3) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }

            AuthenticationUtils::$authenticationMap->set($practoAccountId, $profileToken);

    }
}
