<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\DoctorQuestion;
use ConsultBundle\Entity\UserNotification;
use ConsultBundle\Entity\DoctorNotification;
use ConsultBundle\Response\DoctorNotificationResponseObject;
use ConsultBundle\Response\UserNotificationResponseObject;

/**
 * Notification Manager at Consult
 */
class NotificationManager extends BaseManager
{
    /**
     * @param integer $questionId      - Id of Question Object
     * @param integer $practoAccountId - Practo Account Id of Patient
     * @param string  $text            - Message that was sent to user.
     *
     * @return integer
     */
    public function createPatientNotification($questionId, $practoAccountId, $text)
    {
        $patientNotification = new UserNotification();

        $question = $this->helper->loadById(
            $questionId,
            ConsultConstants::QUESTION_ENTITY_NAME
        );
        $patientNotification->setQuestion($question);
        $patientNotification->setPractoAccountId($practoAccountId);
        $patientNotification->setText($text);

        $this->helper->persist($patientNotification, true);

        return $patientNotification->getId();
    }

    /**
     * @param DoctorQuestion $question        - Question Object
     * @param integer        $practoAccountId - Practo Account Id of Doctor
     * @param string         $text            - Message to be sent to doctor
     *
     * @return integer
     */
    public function createDoctorNotification($question, $practoAccountId, $text = "A question has been assigned to you")
    {
        $doctorNotification = new DoctorNotification();

        $doctorNotification->setQuestion($question);
        $doctorNotification->setPractoAccountId($practoAccountId);
        $doctorNotification->setText($text);

        $this->helper->persist($doctorNotification, true);

        return $doctorNotification->getId();
    }

    /**
     * @param Array $requestParams
     *
     * @return Array
     */
    public function loadDoctorNotificationByFilters($requestParams)
    {

        $limit = (array_key_exists('limit', $requestParams)) ? $requestParams['limit'] : 30;
        $offset = (array_key_exists('offset', $requestParams)) ? $requestParams['offset'] : 0;
        $viewed = (array_key_exists('view', $requestParams)) ? $requestParams['view'] : null;
        $practoAccountId = (array_key_exists('practo_account_id', $requestParams)) ? $requestParams['practo_account_id'] : null;
        $sortBy = (array_key_exists('sort_by', $requestParams)) ? $requestParams['sort_by'] : 'created_at';

        if (null == $practoAccountId) {
            throw new \Exception("Please pass practo_account_id of doctor as query param.");
        }

        $em = $this->helper->getRepository(ConsultConstants::DOCTOR_NOTIFICATION_ENTITY_NAME);
        $notificationList = $em->findDoctorNotificationByFilters($practoAccountId, $viewed, $limit, $offset, $sortBy);

        if (empty($notificationList)) {
            return null;
        }
        $notificationResponseList = array();
        foreach ($notificationList[0] as $notification) {
            $notificationResponse = new DoctorNotificationResponseObject($notification);
            $notificationResponseList[] = $notificationResponse;
        }

        $notificationList[0] = $notificationResponseList;

        return $notificationList;
    }

    /**
     * @param Array $requestParams - Request Parameters
     *
     * @return DoctorNotification
     */
    public function patchDoctorNotification($requestParams)
    {
        $error = array();
        if (array_key_exists('notification_id', $requestParams) and array_key_exists('practo_account_id', $requestParams)) {
            $notification = $this->helper->loadById($requestParams['notification_id'], ConsultConstants::DOCTOR_NOTIFICATION_ENTITY_NAME);

            if ($notification == null) {
                @$error['notification_id'] = "No Notification for this id";
                throw new ValidationError(@$error);
            }
            if ($notification->getPractoAccountId() != intval($requestParams['practo_account_id'])) {
                @$error['invalid_access'] = "You cannot patch this notification";
                throw new ValidationError(@$error);
            }
        } else {
            @$error['notification_id'] = "Please supply notification_id";
            throw new ValidationError(@$error);
        }

        if (!empty(@$error)) {
        }
        if (array_key_exists('view', $requestParams)) {
            $notification->setViewed($requestParams['view']);
        }
        $this->helper->persist($notification, 'true');

        return new DoctorNotificationResponseObject($notification);
    }

    /**
     * @param Array $requestParams
     *
     * @return Array
     */
    public function loadUserNotificationByFilters($requestParams)
    {

        $limit = (array_key_exists('limit', $requestParams)) ? $requestParams['limit'] : 30;
        $offset = (array_key_exists('offset', $requestParams)) ? $requestParams['offset'] : 0;
        $viewed = (array_key_exists('view', $requestParams)) ? $requestParams['view'] : null;
        $practoAccountId = (array_key_exists('practo_account_id', $requestParams)) ? $requestParams['practo_account_id'] : null;
        $sortBy = (array_key_exists('sort_by', $requestParams)) ? $requestParams['sort_by'] : 'created_at';

        if (null == $practoAccountId) {
            throw new \Exception("Please pass practo_account_id of doctor as query param.");
        }

        $em = $this->helper->getRepository(ConsultConstants::USER_NOTIFICATION_ENTITY_NAME);
        $notificationList = $em->findUserNotificationByFilters($practoAccountId, $viewed, $limit, $offset, $sortBy);

        $notificationResponseList = array();
        foreach ($notificationList[0] as $notification) {
            $notificationResponse = new UserNotificationResponseObject($notification);
            $notificationResponseList[] = $notificationResponse;
        }

        $notificationList[0] = $notificationResponseList;

        return $notificationList;
    }

    /**
     * @param Array $requestParams - Request Parameters
     *
     * @return UserNotification
     */
    public function patchUserNotification($requestParams)
    {
        $error = array();
        if (array_key_exists('notification_id', $requestParams) and array_key_exists('practo_account_id', $requestParams)) {
            $notification = $this->helper->loadById($requestParams['notification_id'], ConsultConstants::USER_NOTIFICATION_ENTITY_NAME);

            if ($notification == null) {
                @$error['notification_id'] = "No Notification for this id";
                throw new ValidationError(@$error);
            }

            if ($notification->getPractoAccountId() != $requestParams['practo_account_id']) {
                @$error['invalid_access'] = "You cannot patch this notification";
                throw new ValidationError(@$error);
            }
        } else {
            @$error['notification_id'] = "Please supply notification_id";
            throw new ValidationError(@$error);
        }


        if (array_key_exists('view', $requestParams)) {
            $notification->setViewed($requestParams['view']);
        }
        $this->helper->persist($notification, 'true');

        return new UserNotificationResponseObject($notification);
    }
}
