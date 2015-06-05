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
                                      'date_of_birth' => 'dob',
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

        }catch(\Exception $e)
        {
            //do nothing.
        }

        



    }

    private function populatePostData($params)
    {

        if (array_key_exists('user_profile_details', $params)) {
            if (!(array_key_exists('is_someone_else', $params['user_profile_details']) and
                $params['user_profile_details']['is_someone_else'] === true)) {

                $data = $params['user_profile_details'];
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
                    if(!empty($val))
                    {
                        if($value === 'dob')
                        {

                            $dob = new \DateTime($val);

                            $val = $dob->format("Y-m-d");


                        }
                        $postData[$value] = $val;

                    }
                }catch (\Exception $e)
                {
                    //var_dump($e->getTraceAsString());
                }

            }
        }



        return $postData;
    }

}