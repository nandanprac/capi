<?php

namespace ConsultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Master Controller
 */
class MasterController extends BaseConsultController
{
    /**
     * @param Request $request - request Object
     *
     * @return View
     */
    public function getMasterSpecialtiesAction(Request $request)
    {
        $requestParams = $request->query->all();

        try {
            $masterManager = $this->get('consult.master_manager');
            $masterSpecialityList = $masterManager->loadMasterSpecialties($requestParams);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $masterSpecialityList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return array('specialities' => $masterSpecialityList);
    }

    /**
     * @param Request $request - request Object
     *
     * @return View
     */
    public function getOccupationOptionsAction(Request $request)
    {
        $requestParams = $request->query->all();

        try {
            $masterManager = $this->get('consult.master_manager');
            $occupationsList = $masterManager->loadOccupationOptions($requestParams);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $occupationsList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return array('occupations' => $occupationsList);
    }
}
