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
    public function __construct($accountHost, $accountsSigningKey)
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
        if (!$userInfo->isIsRelative()) {
            $userInfo = $this->retrieveUserProfileNew($userInfo);
            $question->setUserInfo($userInfo);

        }

    }

    /**
     * @param \ConsultBundle\Entity\UserInfo $userInfo
     *
     * @return \ConsultBundle\Entity\User|\ConsultBundle\Entity\UserInfo|null
     */
    public function retrieveUserProfileNew(UserInfo $userInfo)
    {
        if (empty($userInfo) || empty($userInfo->getPractoAccountId())) {
            return null;
        }
        $accountId = $userInfo->getPractoAccountId();
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


        $userInfo = $this->populateUserFromAccounts($res->json(), $userInfo);


        return $userInfo;
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

    /**
     * @param array                          $userProfile
     * @param \ConsultBundle\Entity\UserInfo $user
     *
     * @return \ConsultBundle\Entity\User|\ConsultBundle\Entity\UserInfo|null
     */
    private function populateUserFromAccounts(array $userProfile, UserInfo $user)
    {

        if (is_null($userProfile)) {
            return null;
        }


        if (empty($user)) {
            $user = new UserInfo();
        }






        if (array_key_exists('dob', $userProfile)) {
            $dob = new \DateTime($userProfile['dob']);
            $age = $dob->diff(new \DateTime())->y;
            $user->setAge($age);
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

        if (array_key_exists('name', $userProfile)) {
            $user->setName($userProfile['name']);
        }

        if (array_key_exists('profile_picture', $userProfile)) {
            $user->setName($userProfile['profilePicture']);
        }


        return $user;
    }
}
