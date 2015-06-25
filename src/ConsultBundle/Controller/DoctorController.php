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

/**
 * Doctor Controller
 */
class DoctorController extends BaseConsultController
{

    /**
     * @return ArrayCollection
     *
     */
    public function getDoctorDashboardAction()
	{
		//$this->authenticate();
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
}
