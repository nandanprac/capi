<?php
namespace ConsultBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use ConsultBundle\Manager\ValidationError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Doctor Controller
 */
class DoctorController extends BaseConsultController
{

    /**
     * @return ArrayCollection
     */
    public function getDoctorDashboardAction()
    {
        $this->authenticateForDoctor();
        $request = $this->get('request');
        $queryParams = $request->query->all();
        try {
            $doctorManager = $this->get('consult.doctor_manager');
            $detailList = $doctorManager->loadAllForDoctor($queryParams);
        } catch (\Exception $e) {
            return View::create(json_encode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        if (null === $detailList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return $detailList;
    }

    /**
     * @param int $id
     * @\FOS\RestBundle\Controller\Annotations\View()
     * @return \FOS\RestBundle\View\View
     */
    public function getDoctorConsultSettingsAction($id)
    {
        $this->authenticate();
        $doctorManager = $this->get('consult.doctor_manager');
        try {
            $settings = $doctorManager->getConsultSettings($id);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        }

        if (null === $settings) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return $settings;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @\FOS\RestBundle\Controller\Annotations\View()
     * @return \ConsultBundle\Entity\DoctorConsultSettings|\FOS\RestBundle\View\View
     */
    public function putDoctorConsultSettingsAction(Request $request)
    {
        $this->authenticateForDoctor(true, false);
        $postData = $request->request->all();
        $doctorManager = $this->get('consult.doctor_manager');
        try {
            $settings = $doctorManager->putConsultSettings($postData);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        } catch (HttpException $he) {
            return View::create($he->getMessage(), $he->getStatusCode());
        } catch (\Exception $e) {
            return View::create($e->getMessage(), $e->getCode());
        }

        return $settings;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \FOS\RestBundle\View\View
     * @throws \Exception
     */
    public function postDoctorConsultSettingsAction(Request $request)
    {
        $this->authenticate();
        $postData = $request->request->all();
        $doctorManager = $this->get('consult.doctor_manager');
        try {
            $settings = $doctorManager->putConsultSettings($postData, true);
        } catch (ValidationError $e) {
            return View::create(json_decode($e->getMessage(), true), Codes::HTTP_BAD_REQUEST);
        } catch (HttpException $he) {
            return View::create($he->getMessage(), $he->getStatusCode());
        } catch (\Exception $e) {
            return View::create($e->getMessage(), $e->getCode());
        }

        return $settings;
    }

}
