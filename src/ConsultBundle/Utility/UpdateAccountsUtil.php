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

    private static $fieldsToUpdate = array('weight_in_kgs' => 'weight',
                                     'height_in_cms' => 'height',
                                      'age' => 'dob',
                                     'gender' => 'gender',
                                      'blood_group' => 'blood_group');

    public function __construct($accountHost)
    {
        $this->accountHost = $accountHost;
    }


    public function updateAccountDetails($profileToken, $data)
    {

        //var_dump($profileToken);die;
        if(empty($profileToken))
            return null;


            $postData = $this->populatePostData($data);



        //var_dump($postData);die;

        if(empty($postData))
        {
            return null;
        }

        $body = new PostBody();
        $body->replaceFields($postData);
        $request = new Request('POST', $this->accountHost."/update_profile_with_token",
            array('X-Profile-Token' => $profileToken), $body );

        $client = new Client();

        try{
            $client->send($request);

<<<<<<< HEAD
        } catch (\Exception $e) {
=======
        }catch(\Exception $e)
        {
>>>>>>> master
            //do nothing.
        }

        



    }

    private function populatePostData($params)
    {

<<<<<<< HEAD
        if (array_key_exists('user_info', $params)) {
            if (!(array_key_exists('is_relative', $params['user_info'])
                and Utility::toBool($params['user_info']['is_relative']) )
            ) {
                $data = $params['user_info'];
=======
        if (array_key_exists('user_profile_details', $params)) {
            if (!(array_key_exists('is_someone_else', $params['user_profile_details']) and
                $params['user_profile_details']['is_someone_else'] === true)) {

                $data = $params['user_profile_details'];
>>>>>>> master
            }
        }

        //var_dump($data);die;

        if(empty($data))
            return null;


        //var_dump($data);die;

         $postData = array();
        foreach(self::$fieldsToUpdate as $key => $value)
        {
            if(array_key_exists($key, $data) )
            {
                try{
                    $val = trim($data[$key]);
<<<<<<< HEAD
                    if (!empty($val)) {
                        if ($value === 'dob') {
                            $dob = $this->getCorrectedDOB($val);
                            if (!empty($dob)) {
                                $val = $dob->format("Y-m-d");
                            }
                        }
                        if (!empty($val)) {
                            $postData[$value] = $val;
=======
                    if(!empty($val))
                    {
                        if($value === 'dob')
                        {

                            $dob = new \DateTime($val);

                            $val = $dob->format("Y-m-d");


>>>>>>> master
                        }

                    }
                }catch (\Exception $e)
                {
                    //var_dump($e->getTraceAsString());
                }

            }
        }



        return $postData;
    }

<<<<<<< HEAD
    /**
     * @param \ConsultBundle\Utility\int $age
     *
     * @return \DateTime|null
     */
    private function getCorrectedDOB(int $age)
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
        } else {
            $yearDiff = $age - $currAge;
        }

        $dateInterval = new \DateInterval("P".$yearDiff."Y");

        return $dob->sub($dateInterval);

    }
}
=======
}
>>>>>>> master
