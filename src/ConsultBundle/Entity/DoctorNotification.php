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
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorNotificationRepository")
 * @ORM\Table(name="doctor_notifications")
 * @ORM\HasLifecycleCallbacks()
 */
class DoctorNotification extends BaseEntity
{
    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     */
    protected $practoAccountId;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="$doctorNotifications")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $question;

    /**
     * @ORM\Column(name="notification_text")
     */
    protected $notificationText;

    /**
     * @ORM\Column(type="smallint", name="is_viewed")
     */
    protected $isViewed=0;
}
