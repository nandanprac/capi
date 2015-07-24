<?php

namespace ConsultBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use ConsultBundle\Manager\ValidationError;

/**
 *  PrivateThread Controller
 */
class PrivateThreadController extends BaseConsultController
{

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \FOS\RestBundle\View\View
     * @throws \HttpException
     */
    public function postPrivateThreadAction(Request $request)
    {

        $logger = $this->get('logger');
        $logger->info("Private Thread ".$request);
        $practoAccountId = $this->authenticate();

        $postData = $request->request->get('question');

        $files = $request->files;

        //$practoAccountId = $request->request->get('practo_account_id');

        $profileToken = $request->headers->get('X-Profile-Token');


        $privateThreadManager = $this->get('consult.private_thread_manager');


        try {
            $thread= $privateThreadManager->add((array) json_decode($postData, true), $practoAccountId, $files, $profileToken);

        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        return View::create(
            array("thread" => $thread),
            Codes::HTTP_CREATED
        );
    }

    /**
     * @param int $id
     *
     * @Get("/private/thread/{id}")
     *
     * @return \ConsultBundle\Entity\Question|\FOS\RestBundle\View\View
     */
    public function getPrivateThreadAction($id)
    {
        $logger = $this->get('logger');
        $logger->info("Get Private Question ".$id);
        $practoAccountId = $this->authenticate();

        $privateThreadManager = $this->get('consult.private_thread_manager');

        $privateThread = $privateThreadManager->load($id, $practoAccountId);


        if (null === $privateThread) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return $privateThread;
    }

    /**
     * @param Request $requestRec - request Object
     *
     * @Get("/private/threads")
     *
     * @return array PrivateThreads - list of private question objects
     */
    public function getPrivateThreadsAction(Request $requestRec)
    {
        $practoAccountId = $this->authenticate();
        $privateThreadManager = $this->get('consult.private_thread_manager');
        $privateThreadList = $privateThreadManager->loadAll($practoAccountId, false);


        if (null === $privateThreadList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return $privateThreadList;
    }

    /**
     * @param Request $requestRec - request Object
     *
     * @Get("/doctor/private/threads")
     *
     * @return array PrivateThreads - list of private question objects
     */
    public function getDoctorPrivateThreadsAction(Request $requestRec)
    {
        $practoAccountId = $this->authenticate();
        $privateThreadManager = $this->get('consult.private_thread_manager');

        $privateThreadList = $privateThreadManager->loadAll($practoAccountId, true);


        if (null === $privateThreadList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return $privateThreadList;
    }
}
