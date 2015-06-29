<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 12:43
 */

namespace ConsultBundle\Controller;

use ConsultBundle\Annotations\NeedAuthentication;
use ConsultBundle\Entity\DoctorReply;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as Views;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RepliesController
 *
 * @package ConsultBundle\Controller
 */
class RepliesController extends BaseConsultController
{

    /**
     * @param Request $request
     * @return DoctorReply
     *
     * @View()
     *
     */
    public function postDoctorReplyAction(Request $request)
    {
        $this->authenticate();
        $postData = $request->request->all();
        $doctorReplyManager = $this->get('consult.doctorReplyManager');

        try {
            $doctorReply = $doctorReplyManager->replyToAQuestion($postData);
        } catch (\HttpException $e) {
            return Views::create($e->getMessage(), $e->getCode());
        }

        return $doctorReply;
    }


    /**
     * @param Request $request
     * @return array|Views
     *
     * @View()
     */
    public function patchReplyAction(Request $request)
    {
        $this->authenticate();
        $postData = $request->request->all();
        $doctorReplyManager = $this->get('consult.doctorReplyManager');
        try {
            $doctorReply = $doctorReplyManager->patchDoctorReply($postData);
        } catch (\HttpException $e) {
            return Views::create($e->getMessage(), $e->getCode());
        }

        return  $doctorReply;
    }

    /**
     * @param int $id
     *
     * @return \ConsultBundle\Response\ReplyResponseObject|\FOS\RestBundle\View\View
     */
    public function getReplyAction($id)
    {
        $this->authenticate();

        $doctorReplyManager = $this->get('consult.doctorReplyManager');
        try {
            $doctorReply = $doctorReplyManager->getReplyById($id);
        } catch (\HttpException $e) {
            return Views::create($e->getMessage(), $e->getCode());
        }

        return  $doctorReply;
    }
}
