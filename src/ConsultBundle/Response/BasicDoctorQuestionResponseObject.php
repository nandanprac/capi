<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 15/06/15
 * Time: 21:45
 */

namespace ConsultBundle\Response;

use ConsultBundle\Entity\DoctorQuestion;

/**
 * Class BasicDoctorQuestionResponseObject
 *
 * @package ConsultBundle\Response
 */
class BasicDoctorQuestionResponseObject extends BasicQuestionResponseObject
{

    private $votes;

    private $rating;

    /**
     * @param \ConsultBundle\Entity\DoctorQuestion $doctorQuestion
     */
    public function __construct(DoctorQuestion $doctorQuestion)
    {
        parent::__construct($doctorQuestion->getQuestion());

        $this->setId($doctorQuestion->getId());
        $this->setState($doctorQuestion->getState());


    }

    /**
     * @return mixed
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param mixed $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }



}