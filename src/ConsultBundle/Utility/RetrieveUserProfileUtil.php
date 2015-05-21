<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 21/05/15
 * Time: 12:50
 */

namespace ConsultBundle\Utils;


class RetrieveUserProfileUtil {

    private $accountHost;


    public function __construct($accountHost)
    {
        $this->accountHost = $accountHost;

        if(AuthenticationUtils::$authenticationMap === null)
        {

            AuthenticationUtils::$authenticationMap = new Map();

        }


    }



}