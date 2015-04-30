<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:18
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\PatientNotificationRepository")
 * @ORM\Table(name="patient_notifications")
 * @ORM\HasLifecycleCallbacks()
 */
class PatientNotification extends BaseEntity{

    /**
     * @ORM\Column(type="integer")
     */
    protected $patient_ids;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="$patientNotifications")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $question;

    /**
     * @ORM\Column(name="Notification_txt")
     */
    protected $notificationText;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $isViewed;

}