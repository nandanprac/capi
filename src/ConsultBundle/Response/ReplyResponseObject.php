<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 05/06/15
 * Time: 13:15
 */

namespace ConsultBundle\Response;

use ConsultBundle\Entity\DoctorEntity;

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
    private $votes;

    /**
     * @var integer
     */
    private $rating;

    /**
     * @var
     */
    private $vote;

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
        $this->vote = $vote;
    }
}
