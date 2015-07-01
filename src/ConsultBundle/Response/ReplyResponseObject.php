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
    private $practoAccountId;

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
            $this->practoAccountId = $reply->getDoctorQuestion()->getPractoAccountId();
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
        $this->rating = $this->getInt($rating);
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
        $this->votes = $this->getInt($votes);
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
            $this->vote = $this->getInt($vote);
        }

    }

    /**
     * @return int
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * @param int $practoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $practoAccountId;
    }

    /**
     * @param array $doctorQuestion
     */
    public function setDoctorFromAttributes(array $doctorQuestion)
    {
        if (!empty($doctorQuestion)) {
            $doc = new DoctorEntity();
            $doc->setName($doctorQuestion['name']);
            $doc->setSpeciality($doctorQuestion['speciality']);
            $doc->setProfilePicture($doctorQuestion['profilePicture']);
            $doc->setFabricId($doctorQuestion['doctorId']);

            $this->setDoctor($doc);
        }
    }


}
