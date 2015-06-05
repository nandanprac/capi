<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 21/05/15
 * Time: 12:50
 */

namespace ConsultBundle\Utility;

use ConsultBundle\Entity\Question;
use ConsultBundle\Entity\User;
use ConsultBundle\Entity\UserInfo;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Post\PostBody;

/**
 * Class RetrieveUserProfileUtil
 *
 * @package ConsultBundle\Utility
 */
class RetrieveUserProfileUtil
{

    private $accountHost;
    private $accountsSigningKey;


    /**
     * @param string $accountHost
     * @param string $accountsSigningKey
     */
    public function __construct($accountHost = 'http://accounts.practo.local', $accountsSigningKey = 'software-accounts-key')
    {
        $this->accountHost = $accountHost;

        $this->accountsSigningKey = $accountsSigningKey;

    }




    /**
     * @param \ConsultBundle\Entity\Question $question
     */
    public function loadUserDetailInQuestion(Question $question)
    {

        $userInfo = $question->getUserInfo();
        if (is_null($userInfo)) {
            $userInfo = new UserInfo();
        }

        $userProfile = $userInfo->getUserProfileDetails();

        if (is_null($userProfile)) {
            $userProfile = $this->retrieveUserProfileNew($question->getPractoAccountId());

            $userInfo->setUserProfileDetails($userProfile);
            $question->setUserInfo($userInfo);
        }


    }


    /**
     * @param int $accountId
     *
     * @return \ConsultBundle\Entity\User
     */
    public function retrieveUserProfileNew($accountId)
    {
        $postData = array(
            'service'           => 'software',
            'practo_account_id' => $accountId,
            'signed'            => 'service,practo_account_id,signed'
        );




        $postData = $this->signEndpointPostData($postData, $this->accountsSigningKey);

        $body = new PostBody();
        $body->replaceFields($postData);
        $request = new Request('POST', $this->accountHost."/_enquire_profile", [], $body);


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
            $signedData[] = $key.'='.urlencode($postData[$key]);
        }
        $signedData = implode('&', $signedData);

        $postData['signature'] = base64_encode(hash_hmac('sha1', $signedData, $signingKey, true));

        return $postData;
    }

    private function populateUserFromAccounts(array $userProfile)
    {

        if (is_null($userProfile)) {
            return null;
        }



        $user = new User();





        if (array_key_exists('dob', $userProfile)) {
            $user->setDateOfBirth($userProfile['dob']);
        }

        if (array_key_exists('gender', $userProfile)) {
            $user->setGender($userProfile['gender']);
        }

        if (array_key_exists('height', $userProfile)) {
            $user->setHeightInCms($userProfile['height']);
        }

        if (array_key_exists('weight', $userProfile)) {
            $user->setWeightInKgs($userProfile['weight']);
        }

        if (array_key_exists('blood_group', $userProfile)) {
            $user->setBloodGroup($userProfile['blood_group']);

        }



        return $user;


    }


}
