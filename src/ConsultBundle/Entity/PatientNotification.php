<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:18
 */

namespace ConsultORMBundle\Entity;

/**
 * @ORM\Entity(repositoryClass="ConsultORMBundle\Repository\PatientNotificationRepository")
 * @ORM\Table(name=Patient_Notification)
 * @ORM\HasLifecycleCallbacks()
 */
class PatientNotification extends BaseEntity{

    /**
     * @ORM\Column(type="integer")
     */
    protected $patient_id;

    /**
     *  @ManyToOne(targetEntity="QuestionEntity", inversedBy="$patientNotifications")
     * @JoinColumn(name="question_id", referencedColumnName="id")
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