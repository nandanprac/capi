<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 12:43
 */

namespace ConsultBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;

class UserController extends FOSRestController{

    /**
     * @param $userId
     * @param $questionId
     * @param $hasChanged
     * @param $allergies
     * @param $medications
     * @param $diagnosedConditions
     *
     * @View()
     */
    public function postAdditionalUserInfoAction($userId, $questionId, $hasChanged, $allergies, $medications, $diagnosedConditions)
    {
        //TODO By Sahana
    }

    /**
     * @param $userId
     *
     * @View()
     */
    public function getAdditionalUserInfoAction($userId)
    {
        //TODO By Sahana
    }

    /**
     * @param $userId
     * @param $changes
     *
     * @View()
     */
    public function postUserProfileAction($userId, $changes)
    {
        //TODO By Sahana
    }

    /**
     * @param $userId
     *
     * @View()
     */
    public function getUserProfileAction($userId)
    {
        //TODO By Sahana
    }

}