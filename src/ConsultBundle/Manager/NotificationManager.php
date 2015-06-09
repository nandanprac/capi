<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\UserNotification;
use ConsultBundle\Entity\DoctorNotification;

/**
 * Notification Manager at Consult
 */
class NotificationManager extends BaseManager
{
    /**
     * @param integer $questionId      - Id of Question Object
     * @param integer $practoAccountId - Practo Account Id of Patient
     * @param string  $text            - Message that was sent to user.
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
    }

    /**
     * @param Question $question        - Question Object
     * @param integer  $practoAccountId - Practo Account Id of Doctor
     * @param string   $text            - Message to be sent to doctor
     */
    public function createDoctorNotification($question, $practoAccountId, $text = "A question has been assigned to you")
    {
        $doctorNotification = new DoctorNotification();

        $doctorNotification->setQuestion($question);
        $doctorNotification->setPractoAccountId($practoAccountId);
        $doctorNotification->setText($text);

        $this->helper->persist($doctorNotification, true);
    }
}
