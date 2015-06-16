<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 15/06/15
 * Time: 21:45
 */

namespace ConsultBundle\Response;

use ConsultBundle\Entity\DoctorQuestion;
use ConsultBundle\Utility\Utility;

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
     * @var boolean
     */
    private $hasImages;

    /**
     * @param \ConsultBundle\Entity\DoctorQuestion $doctorQuestion
     */
    public function __construct(DoctorQuestion $doctorQuestion)
    {
        $question = $doctorQuestion->getQuestion();
        parent::__construct($question);


        $this->setId($doctorQuestion->getId());
        $this->setState($doctorQuestion->getState());
        $this->setHasImages(($question->getImages()->count()));


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

    /**
     * @return boolean
     */
    public function isHasImages()
    {
        return $this->hasImages;
    }

    /**
     * @param boolean $hasImages
     */
    public function setHasImages($hasImages)
    {
        $this->hasImages = Utility::toBool($hasImages);
    }





}