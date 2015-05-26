<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 25/05/15
 * Time: 18:42
 */

namespace ConsultBundle\Utility;

use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Post\PostBody;


class UpdateAccountsUtil {

    private $accountHost;

    private static $fieldsToUpdate = array('weightInKgs' => 'weight',
                                     'heightInCms' => 'height',
                                      'dob' => 'dob');

    public function __construct($accountHost)
    {
        $this->accountHost = $accountHost;
    }


    public function updateAccountDetails($profileToken, $data)
    {


        $postData = $this->populatePostData($data);

        $body = new PostBody();
        $body->replaceFields($postData);
        $request = new Request('POST', $this->accountHost."/update_profile_with_token",
            array('X-Profile-Token' => $profileToken), $body );

        $client = new Client();
        $res = $client->send($request);

        var_dump($res->json());die;



    }

    private function populatePostData($data)
    {
         $postData = array();
        foreach(self::$fieldsToUpdate as $key => $value)
        {
            if(array_key_exists($key, $data))
            {
                $postData[$value] = $data[$key];
            }
        }

        if(count($postData)===0)
            return null;

        return $postData;
    }

}