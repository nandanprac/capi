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


    /**
     * @var array
     */
    private static $authenticationMap;

    private $accountHost;


    /**
     * @param string $accountHost
     */
    public function __construct($accountHost)
    {
        $this->accountHost = $accountHost;

        if (AuthenticationUtils::$authenticationMap === null) {
            AuthenticationUtils::$authenticationMap = new Map();

        }


    }

    /**
     * @param int    $practoAccountId
     * @param string $profileToken
     *
     * @return bool
     */
    public function authenticateWithAccounts($practoAccountId, $profileToken)
    {

        if ($this->isAlreadyValidated($practoAccountId, $profileToken)) {
            return true;
        }

        return $this->validateWithTokenNew($practoAccountId, $profileToken);
    }

    /**
     * @param $practoAccountId
     * @param $profileToken
     *
     * @return bool
     */
    private function isAlreadyValidated($practoAccountId, $profileToken)
    {
        return ($profileToken === AuthenticationUtils::$authenticationMap[$practoAccountId]);
    }


    /**
     * @param $practoAccountId
     * @param $profileToken
     *
     * @return bool
     */
    private function validateWithTokenNew($practoAccountId, $profileToken)
    {

        $client = new Client(
            array('base_url' => $this->accountHost,
                'defaults' => array('headers' => array('X-Profile-Token' => $profileToken)))
        );
        $res = $client->get('/get_profile_with_token');


        $userJson = $res->json();

        $userId = $userJson["id"];


        $code = $res->getStatusCode();

        if (is_null($userId) || $userId != $practoAccountId || $code[0] > 3) {
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }

        AuthenticationUtils::$authenticationMap[$practoAccountId] =  $profileToken;

        return true;

    }
}
