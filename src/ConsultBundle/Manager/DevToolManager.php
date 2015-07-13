<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 13/07/15
 * Time: 18:02
 */

namespace ConsultBundle\Manager;


use ConsultBundle\Constants\ConsultConstants;

class DevToolManager extends BaseManager
{

    public function executeQuery($postData)
    {
        $practoAccountId = $_SESSION['authenticated_user']['id'];
        $result = $this->helper->loadById($practoAccountId, ConsultConstants::ADMIN_TABLE);
        //TODO check if exist in admin table
        $select = $postData['select'];
        $from =  $postData['from'];
        $where = $postData['where'];


    }

}