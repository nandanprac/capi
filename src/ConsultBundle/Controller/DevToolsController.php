<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 13/07/15
 * Time: 18:00
 */

namespace ConsultBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class DevToolsController extends BaseConsultController{

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function postSQLQueryAction(Request $request)
    {
       $postData = $request->request->all();
        $devToolsManager = $this->get('consult.dev_tools_manager');
        $result = $devToolsManager->executeQuery($postData);
        return $result;
    }

}