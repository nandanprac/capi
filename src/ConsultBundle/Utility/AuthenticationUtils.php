<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 18/05/15
 * Time: 11:23
 */

namespace ConsultBundle\Utility;


use Buzz\Browser;
use Buzz\Bundle\BuzzBundle\Buzz\BrowserManager;
use Buzz\Bundle\BuzzBundle\Buzz\Buzz;
use Buzz\Exception\RequestException;
use GuzzleHttp\Client;
use PhpCollection\Map;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthenticationUtils {


    private static  $authenticationMap;

    private $accountHost;




    public function __construct($accountHost)
    {
        $this->accountHost = $accountHost;

        if(AuthenticationUtils::$authenticationMap === null)
        {

            AuthenticationUtils::$authenticationMap = new Map();

        }


    }

    /**
     * @param $practoAccountId
     * @param $profileToken
     * @return bool
     */
    public function authenticateWithAccounts($practoAccountId, $profileToken)
    {
        if($this->isAlreadyValidated($practoAccountId, $profileToken))
            return true;

        return $this->validateWithTokenNew($practoAccountId, $profileToken);
    }

    /**
     * @param $practoAccountId
     * @param $profileToken
     * @return bool
     */
    private function isAlreadyValidated($practoAccountId, $profileToken)
    {
        //var_dump($practoAccountId, AuthenticationUtils::$authenticationMap->get($practoAccountId));
       /* var_dump($profileToken, "    ", AuthenticationUtils::$authenticationMap->get($practoAccountId));
        var_dump($profileToken === AuthenticationUtils::$authenticationMap->get($practoAccountId));die;*/
        return ($profileToken === AuthenticationUtils::$authenticationMap->get($practoAccountId));
    }

    /**
     * @param $practoAccountId
     * @param $profileToken
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

            $client = new Client([
                'base_url' => $this->accountHost,
                'defaults' => [
                    'headers' => ['X-Profile-Token' => $profileToken]
                ]]);
            $res = $client->get('/get_profile_with_token');

        
        $userJson = $res->json();

        $userId = $userJson["id"];


        $code = $res->getStatusCode();

        if(is_null($userId) || $userId != $practoAccountId || $code[0] > 3)
        {
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }

        AuthenticationUtils::$authenticationMap->set($practoAccountId, $profileToken);

    }

}