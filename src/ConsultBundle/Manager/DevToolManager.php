<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 13/07/15
 * Time: 18:02
 */

namespace ConsultBundle\Manager;


use ConsultBundle\Constants\ConsultConstants;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Util\Codes;

class DevToolManager extends BaseManager
{
    /*
        This function checks the practo_account_id in the POST request with the practo_account_id in the
        admin_users table. If entry is found, it forms a select query based on the user request and calls  
        executeNativeSQLQuery in Helper.php to execute the query. Otherwise, it throws HTTP_FORBIDDEN exception.
    */


    /**
     * @param $postData
     * @return array
     * @throws \HttpException
     */

    public function executeQuery($postData)
    {
        $practoAccountId = $_SESSION['authenticated_user']['id'];
        
        $result = $this->helper->getRepository(ConsultConstants::ADMIN_TABLE)->findOneBy(array('practoAccountId' => $practoAccountId));

        if(empty($result))
             throw new HttpException(Codes::HTTP_FORBIDDEN, "Permission Denied");

        $select = $postData['select'];
        $from = $postData['from'];
        $where = $postData['where'];

        if(empty($where))
            return $this->helper->executeNativeSQLQuery('SELECT '.$select.' FROM '.$from);
        else
            return $this->helper->executeNativeSQLQuery('SELECT '.$select.' FROM '.$from.' WHERE '.$where);        
    }
}