<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 05/06/15
 * Time: 13:15
 */

namespace ConsultBundle\Response;

use ConsultBundle\Entity\DoctorEntity;
use ConsultBundle\Entity\DoctorReply;

/**
 * Class ReplyResponseObject
 *
 * @package ConsultBundle\Response
 */
class ReplyResponseObject extends ConsultResponseObject
{
    /**
     * @var DoctorEntity $doctor
     */
    private $doctor;

    /**
     * @var int
     */
    private $doctorId;

    /**
     * @var string $text
     */
    private $text;

    /**
     * @var integer
     */
    private $votes=0;

    /**
     * @var integer
     */
    private $rating;

    /**
     * @var int
     */
    private $vote = 0;

    /**
     * @param \ConsultBundle\Entity\DoctorReply $reply
     */
    public function __construct(DoctorReply $reply = null)
    {
        if (!empty($reply)) {
            parent::__construct($reply);
            $this->doctorId = $reply->getDoctorQuestion()->getPractoAccountId();
            $this->text = $reply->getText();
            $this->rating = $reply->getRating();
        }

    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return DoctorEntity
     */
    public function getDoctor()
    {
        return $this->doctor;
    }

    /**
     * @param DoctorEntity $doctor
     */
    public function setDoctor($doctor)
    {
        $this->doctor = $doctor;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param int $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * @return int
     */
    public function getDoctorId()
    {
        return $this->doctorId;
    }

    /**
     * @param int $doctorId
     */
    public function setDoctorId($doctorId)
    {
        $this->doctorId = $doctorId;
    }

    /**
     * @return mixed
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * @param mixed $vote
     */
    public function setVote($vote)
    {
        if (!empty($vote)) {
            $this->vote = $vote;
        }

    }
}
