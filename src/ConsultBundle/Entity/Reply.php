<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:49
 */

namespace ConsultORMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultORMBundle\Repository\ReplyRepository")
 * @ORM\Table(name=Reply)
 */
class Reply extends ConsultEntity{

  /**
   * @ORM\OneToOne(targetEntity="DoctorQuestions" inversedBy = "reply")
   */

  protected $doctorQuestion;

}