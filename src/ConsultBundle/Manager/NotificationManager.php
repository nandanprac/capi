<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\UserNotification;

class NotificationManager extends BaseManager
{
	public function createPatientNotification($questionId, $practoAccountId, $text){
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
}
