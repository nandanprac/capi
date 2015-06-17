<?php

namespace ConsultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Notification Controller
 */
class NotificationController extends BaseConsultController
{
    /**
     * @param Request $request - request Object
     *
     * @return View
     */
    public function getDoctorNotificationsAction(Request $request)
    {
        $this->authenticate();
        $requestParams = $request->query->all();

        try {
            $notificationManager = $this->get('consult.notification_manager');
            $notificationList = $notificationManager->loadDoctorNotificationByFilters($requestParams);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $notificationList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return array('notification' => $notificationList[0], 'count' => $notificationList[1]);
    }

    /**
     * @return DoctorNotification
     */
    public function patchDoctorNotificationAction()
    {
        $this->authenticate();
        $requestParams = $this->getRequest()->request->all();

        try {
            $notificationManager = $this->get('consult.notification_manager');
            $notificationFinal = $notificationManager->patchDoctorNotification($requestParams);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $notificationFinal) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return View::create(array('notification' => $notificationFinal), Codes::HTTP_CREATED);
    }

    /**
     * @param Request $request - request Object
     *
     * @return View
     */
    public function getUserNotificationAction(Request $request)
    {
        $this->authenticate();
        $requestParams = $request->query->all();

        try {
            $notificationManager = $this->get('consult.notification_manager');
            $notificationList = $notificationManager->loadUserNotificationByFilters($requestParams);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $notificationList) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return array('notification' => $notificationList[0], 'count' => $notificationList[1]);

    }

    /**
     * @return DoctorNotification
     */
    public function patchUserNotificationAction()
    {
        $this->authenticate();
        $requestParams = $this->getRequest()->request->all();

        try {
            $notificationManager = $this->get('consult.notification_manager');
            $notificationFinal = $notificationManager->patchUserNotification($requestParams);
        } catch (AccessDeniedException $e) {
            return View::create($e->getMessage(), Codes::HTTP_FORBIDDEN);
        }

        if (null === $notificationFinal) {
            return View::create(null, Codes::HTTP_NOT_FOUND);
        }

        return View::create(array('notification' => $notificationFinal), Codes::HTTP_CREATED);
    }
}
