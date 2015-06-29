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

/**
 * Class UpdateAccountsUtil
 *
 * @package ConsultBundle\Utility
 */
class UpdateAccountsUtil
{

    private $accountHost;

    private static $fieldsToUpdate = array('weight_in_kgs' => 'weight',
                                     'height_in_cms' => 'height',
                                      'age' => 'dob',
                                     'gender' => 'gender',
                                      'blood_group' => 'blood_group');

    /**
     * @param string $accountHost
     */
    public function __construct($accountHost)
    {
        $this->accountHost = $accountHost;
    }

    /**
     * @param string $profileToken
     * @param array  $data
     *
     * @return null
     */
    public function updateAccountDetails($profileToken, $data)
    {

        if (empty($profileToken)) {
            return null;
        }


            $postData = $this->populatePostData($data);




        if (empty($postData)) {
            return null;
        }

        $body = new PostBody();
        $body->replaceFields($postData);
        $request = new Request(
            'POST',
            $this->accountHost."/update_profile_with_token",
            array('X-Profile-Token' => $profileToken),
            $body
        );

        $client = new Client();

        try {
            $userJson = $client->send($request);
            $_SESSION['authenticated_user'] = $userJson;

        } catch (\Exception $e) {
            //do nothing.
        }





    }

    /**
     * @param $data
     *
     * @return array|null
     * @throws \Exception
     */
    private function populatePostData($data)
    {



        if (empty($data)) {
            return null;
        }



         $postData = array();

        foreach (self::$fieldsToUpdate as $key => $value) {
            if (array_key_exists($key, $data)) {
                try {
                    $val = trim($data[$key]);
                    if (!empty($val)) {
                        if ($value === 'dob') {
                            $dob = $this->getCorrectedDOB(intval($val));
                            if (!empty($dob)) {
                                $val = $dob->format("Y-m-d");
                            } else {
                                $val = null;
                            }
                        }
                        if (!empty($val)) {
                            $postData[$value] = $val;
                        }

                    }
                } catch (\Exception $e) {

                }

            }
        }



        return $postData;
    }

    /**
     * @param int $age
     *
     * @return \DateTime|null
     */
    private function getCorrectedDOB($age)
    {
        $dobStr = $_SESSION['authenticated_user']['dob'];

        if (empty($dobStr)) {
            $dob = new \DateTime();
        } else {
            $dob = new \DateTime($dobStr);
        }


        $currAge = $dob->diff(new \DateTime())->y;

        if ($currAge == $age) {
            return null;
        } elseif ($age > $currAge) {
            $yearDiff = $age - $currAge;
            $dateInterval = new \DateInterval("P".$yearDiff."Y");
            $dob->sub($dateInterval);
        } else {
            $yearDiff = $currAge - $age;
            $dateInterval = new \DateInterval("P".$yearDiff."Y");
            $dob->add($dateInterval);
        }



        return $dob;

    }
}
