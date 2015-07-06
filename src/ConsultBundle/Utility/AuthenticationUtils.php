<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 18/05/15
 * Time: 11:23
 */

namespace ConsultBundle\Utility;

use GuzzleHttp\Client;
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
    // private static $authenticationMap;

    private $accountHost;


    /**
     * @param string $accountHost
     */
    public function __construct($accountHost)
    {
        $this->accountHost = $accountHost;

    }

    /**
     * @param int    $practoAccountId
     * @param string $profileToken
     *
     * @return bool
     */
    public function authenticateWithAccounts($practoAccountId, $profileToken)
    {
        return $this->validateWithTokenNew($practoAccountId, $profileToken);
    }

    /**
     * @param $practoAccountId
     * @param $profileToken
     *
     * @return bool

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
        $pId = intval($practoAccountId);
        if (array_key_exists('authenticated_user', $_SESSION)) {
            $userJson = $_SESSION['authenticated_user'];

            if (!empty($userJson) && array_key_exists("id", $userJson)) {
                $id = $userJson['id'];
                if ($id == $pId) {
                    $_SESSION['validated'] = true;

                    return true;
                }

            }
        }

        $client = new Client(
            array('base_url' => $this->accountHost,
                'defaults' => array('headers' => array('X-Profile-Token' => $profileToken)))
        );
        $res = $client->get('/get_profile_with_token');


        $userJson = $res->json();

        $userId = $userJson["id"];


        $code = $res->getStatusCode();

        if (!(empty($userId) || $userId != $practoAccountId || $code[0] > 3)) {
            $_SESSION['validated'] = true;
            $_SESSION['authenticated_user'] = $userJson;
        }

        return true;

    }
}
