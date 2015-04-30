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
 * @ORM\Entity(repositoryClass="ConsultORMBundle\Repository\ReplyRatingsRepository")
 * @ORM\Table(name=Reply_Ratings)
 */
class ReplyRatings extends BaseEntity{

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $userId;

    /**
     * @ORM\ManyToOne(targetEntity="Reply" inversedBy="ratings")
     * @ORM\JoinColumn(name="reply_id" refrencedColumnName="id")
     */
    protected $reply;

    /**
     * @Column(type="smallint")
     */
    protected $rating;
}