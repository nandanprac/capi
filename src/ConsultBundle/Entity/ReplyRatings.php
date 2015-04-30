<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:49
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\ReplyRatingsRepository")
 * @ORM\Table(name="reply_ratings")
 */
class ReplyRatings extends BaseEntity{

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $userId;

    /**
     * @ORM\ManyToOne(targetEntity="Reply", inversedBy="ratings")
     * @ORM\JoinColumn(name="reply_id", referencedColumnName="id")
     */
    protected $reply;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $rating;
}