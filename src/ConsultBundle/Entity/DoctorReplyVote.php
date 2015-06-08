<?php

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorReplyVote
 *
 * @ORM\Table(name="doctor_replies_votes")
 * @ORM\Entity
 */
class DoctorReplyVote extends BaseEntity
{
    /**
     * @var integer
     * @ORM\ManyToMany(targetEntity="ConsultBundle\Entity\DoctorReply")
     * @ORM\JoinColumn(name="reply_id", referencedColumnName="id")
     */
    private $reply;


    /**
     * @var integer
     *
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    private $practoAccountId;

    /**
     * @var integer
     *
     * @ORM\Column(name="vote", type="smallint")
     */
    private $vote;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set practoAccountId
     *
     * @param integer $practoAccountId
     * @return DoctorReplyVote
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $practoAccountId;

        return $this;
    }

    /**
     * Get practoAccountId
     *
     * @return integer
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * Set vote
     *
     * @param integer $vote
     * @return DoctorReplyVote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return integer
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * @return int
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * @param int $reply
     */
    public function setReply($reply)
    {
        $this->reply = $reply;
    }




}
