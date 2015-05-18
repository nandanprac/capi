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
use GuzzleHttp\Client;
use PhpCollection\Map;

class AuthenticationUtils {


    private static  $authenticationMap;
    private $browser;



    public function __construct()
    {
        if(AuthenticationUtils::$authenticationMap ===null)
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
        return $profileToken === AuthenticationUtils::$authenticationMap->get($practoAccountId);
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
            'base_url' => 'http://accounts-consult.practodev.com',
            'defaults' => [
                'headers' => ['X-Profile-Token' => $profileToken]
            ]]);
        $res = $client->get('/get_profile_with_token');

    }

}