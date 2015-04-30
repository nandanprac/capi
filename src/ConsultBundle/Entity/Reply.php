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
class Reply extends BaseEntity{

  /**
   * @ORM\OneToOne(targetEntity="DoctorQuestions" inversedBy = "reply")
   */

  protected $doctorQuestion;

    /**
     * @ORM\Column(type="text", name="answer_text")
     */
    protected $answerText;


    /**
     * @ORM\Column(type="smallint", name="is_selected")
     */
    protected $isSelected = 0;

    /**
     * @ORM\Column(type="datetime", name="viewed_at", nullable=true)
     */
    protected $viewedAt;

    /**
     * @ORM\OneToMany(targetEntity="ReplyRatings" mappedBy="reply")
     */
    protected $ratings;

}