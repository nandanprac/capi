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

    private $hasAdditionalInfo=true;

    /**
     * @param \ConsultBundle\Entity\DoctorQuestion $doctorQuestion
     */
    public function __construct(DoctorQuestion $doctorQuestion)
    {
        if (!empty($doctorQuestion)) {
            $question = $doctorQuestion->getQuestion();
            parent::__construct($question);


            $this->setId($doctorQuestion->getId());
            $this->setState($doctorQuestion->getState());
            $this->setHasImages(($question->getImages()->count()));

            $reply = $doctorQuestion->getDoctorReplies();
            if (!empty($reply) && !$reply->isSoftDeleted()) {
                $rating = $doctorQuestion->getDoctorReplies()->getRating();
                $this->setRating($rating);
            }

        }


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
        $this->votes = $this->getInt($votes);
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
        $this->rating = $this->getInt($rating);
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

    /**
     * @return mixed
     */
    public function getHasAdditionalInfo()
    {
        return $this->hasAdditionalInfo;
    }

    /**
     * @param mixed $hasAdditionalInfo
     */
    public function setHasAdditionalInfo($hasAdditionalInfo)
    {
        $this->hasAdditionalInfo = $hasAdditionalInfo;
    }
}
