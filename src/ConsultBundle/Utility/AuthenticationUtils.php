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

class AuthenticationUtils {


<<<<<<< HEAD
    /**
     * @var array
     */
   // private static $authenticationMap;
=======
    private static  $authenticationMap;
>>>>>>> master

    private $accountHost;




    public function __construct($accountHost)
    {
        $this->accountHost = $accountHost;

<<<<<<< HEAD
=======
        if(AuthenticationUtils::$authenticationMap === null)
        {

            AuthenticationUtils::$authenticationMap = new Map();

        }


>>>>>>> master
    }

    /**
     * @param $practoAccountId
     * @param $profileToken
     * @return bool
     */
    public function authenticateWithAccounts($practoAccountId, $profileToken)
    {
<<<<<<< HEAD
=======
        if($this->isAlreadyValidated($practoAccountId, $profileToken))
            return true;

>>>>>>> master
        return $this->validateWithTokenNew($practoAccountId, $profileToken);
    }

    /**
     * @param $practoAccountId
     * @param $profileToken
     * @return bool

    private function isAlreadyValidated($practoAccountId, $profileToken)
    {
<<<<<<< HEAD
        return ($profileToken === AuthenticationUtils::$authenticationMap[$practoAccountId]);
=======
        //var_dump($practoAccountId, AuthenticationUtils::$authenticationMap->get($practoAccountId));
       /* var_dump($profileToken, "    ", AuthenticationUtils::$authenticationMap->get($practoAccountId));
        var_dump($profileToken === AuthenticationUtils::$authenticationMap->get($practoAccountId));die;*/
        return ($profileToken === AuthenticationUtils::$authenticationMap->get($practoAccountId));
>>>>>>> master
    }

    /**
     * @param $practoAccountId
     * @param $profileToken
<<<<<<< HEAD
     *
=======
>>>>>>> master
     * @return bool
     */
    private function validateWithToken($practoAccountId, $profileToken)
    {
        var_dump($profileToken);die;
        //$browser = new Browser();
        //$browser->h
        //$response = $this->browser->get("http://accounts-consult.practodev.com".'/get_profile_with_token', ['X-Profile-Token' => $profileToken]);
        //$client = new Client(["base_url" => "https://accounts-consult.practodev.com", 'defaults' => ['headers' => ['X-Profile-Token' => $profileToken]]]);
        //$response = $client->get('/get_profile_with_token');
        //var_dump($response->getHeader("content-type"));die;
        return false;
    }


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

<<<<<<< HEAD
            }
        }

        $client = new Client(
            array('base_url' => $this->accountHost,
                'defaults' => array('headers' => array('X-Profile-Token' => $profileToken)))
        );
        $res = $client->get('/get_profile_with_token');
=======
            $client = new Client([
                'base_url' => $this->accountHost,
                'defaults' => [
                    'headers' => ['X-Profile-Token' => $profileToken]
                ]]);
            $res = $client->get('/get_profile_with_token');
>>>>>>> master

        
        $userJson = $res->json();

<<<<<<< HEAD
        $userJson = $res->json();

=======
>>>>>>> master
        $userId = $userJson["id"];


        $code = $res->getStatusCode();

<<<<<<< HEAD
        if (!(empty($userId) || $userId != $practoAccountId || $code[0] > 3)) {
            $_SESSION['validated'] = true;
            $_SESSION['authenticated_user'] = $userJson;
        }

        return true;
=======
        if(is_null($userId) || $userId != $practoAccountId || $code[0] > 3)
        {
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }

        AuthenticationUtils::$authenticationMap->set($practoAccountId, $profileToken);
>>>>>>> master

    }

}