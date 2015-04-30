<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:18
 */

namespace ConsultORMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultORMBundle\Repository\DoctorNotificationRepository")
 * @ORM\Table(name=Doctor_Notification)
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorNotification extends ConsultEntity {

    /**
     * @ORM\Column(type="integer")
     */
    protected $doctor_id;

    /**
     *  @ManyToOne(targetEntity="QuestionEntity", inversedBy="$doctorNotifications")
     * @JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $question;

    /**
     * @ORM\Column(name="Notification_txt")
     */
    protected $notificationText;

    /**
     * @ORM\Column(type="byte")
     */
    protected $isViewed;
}