<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 21/05/15
 * Time: 12:50
 */

namespace ConsultBundle\Utility;

use ConsultBundle\Entity\User;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Post\PostBody;


class RetrieveUserProfileUtil {

    private $accountHost;
    private $accountsSigningKey;
    private static $arrayMapping = array("id" => "practo_accountId",
                                  "blood_group" => "blood_group",
                                  "height" => "height",
                                  "weight" => "weight",
                                  "dob" => "date_of_birth",
                                  "gender" => "gender"
          );



    public function __construct($accountHost = 'http://accounts.practo.local', $accountsSigningKey = 'software-accounts-key')
    {
        $this->accountHost = $accountHost;

        $this->accountsSigningKey = $accountsSigningKey;

    }


    public function retrieveUserProfile($accountId)
    {
        $postData = array(
            'service'           => 'software',
            'practo_account_id' => $accountId,
            'signed'            => 'service,practo_account_id,signed'
        );
        $this->signEndpointPostData($postData, $this->accountsSigningKey);
        $practoDomain = new PractoDomain($this->request);
        $accountsHost = $practoDomain->getHost('accounts');
        /**
         * @var Response $response
         */
        $response = $this->browser->submit($accountsHost. "/_enquire_profile", $postData);
        if (!$response->isSuccessful()) {
            return null;
        }
        var_dump($response->getContent());
    }


    public function retrieveUserProfileNew($accountId)
    {
        $postData = array(
            'service'           => 'software',
            'practo_account_id' => $accountId,
            'signed'            => 'service,practo_account_id,signed'
        );




        $this->signEndpointPostData($postData, $this->accountsSigningKey);

        $body = new PostBody();
        $body->replaceFields($postData);
        $request = new Request('POST', $this->accountHost."/_enquire_profile", [], $body );

        //var_dump($postData);die;

        $client = new Client();
        $res = $client->send($request);

        $user = $this->populateUserFromAccounts($res->json());

        return $user;
    }

    /**
     * @param $postData
     * @param $signingKey
     */
    private function signEndpointPostData(&$postData, $signingKey)
    {
        $signedData = array();
        $urlKeys = explode(',', $postData['signed']);
        foreach ($urlKeys as $key) {
            $signedData[] = $key . '=' . urlencode($postData[$key]);
        }
        $signedData = implode('&', $signedData);

        $postData['signature'] = base64_encode(hash_hmac('sha1', $signedData, $signingKey, true));
    }

    private function populateUserFromAccounts(array $userProfile)
    {

        if(is_null($userProfile))
        {
            return null;
        }



        $user = new User();



        $user->setPractoAccountId($userProfile['id']);
        $user->setDateOfBirth($userProfile['dob']);
        $user->setGender($userProfile['gender']);
        /*$user->setHeight($userProfile['height']);
        $user->setWeight($userProfile['weight']);
        $user->setBloodGroup($userProfile['blood_group']);
        */


        return $user;


    }



}