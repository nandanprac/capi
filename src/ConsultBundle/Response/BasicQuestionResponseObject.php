<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 05/06/15
 * Time: 12:41
 */

namespace ConsultBundle\Response;

use ConsultBundle\Entity\Question;

/**
 * Class QuestionResponseObject
 *
 * @package ConsultBundle\Response
 */
class BasicQuestionResponseObject extends ConsultResponseObject
{

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string $text
     */
    private $text;

    /**
     * @var string $text
     */
    private $speciality;

    /**
     * @var string $text
     */
    private $state;

    /**
     * @var int $viewCount
     */
    private $viewCount;

    /**
     * @var int $shareCount
     */
    private $shareCount;

    /**
     * @var int $bookmarkCount
     */
    private $bookmarkCount;

    /**
     * @var \DateTime
     */
    private $viewedAt;


    /**
     * @param \ConsultBundle\Entity\Question $questionEntity
     */
    public function __construct(Question $questionEntity = null)
    {
        parent::__construct($questionEntity);

        if (!is_null($questionEntity)) {
            $this->setSpeciality($questionEntity->getSpeciality());
            $this->setViewCount($questionEntity->getViewCount());
            $this->state = $questionEntity->getState();
            $this->setSubject($questionEntity->getSubject());
            $this->setText($questionEntity->getText());
            $this->viewedAt = $questionEntity->getViewedAt();
        }

    }

    /**
     * @return \DateTime
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    /**
     * @param \DateTime $viewedAt
     */
    public function setViewedAt($viewedAt)
    {
        $this->viewedAt = $viewedAt;
    }



    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * @param int $viewCount
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
    }

    /**
     * @return int
     */
    public function getShareCount()
    {
        return $this->shareCount;
    }

    /**
     * @param int $shareCount
     */
    public function setShareCount($shareCount)
    {
        $this->shareCount = $shareCount;
    }

    /**
     * @return int
     */
    public function getBookmarkCount()
    {
        return $this->bookmarkCount;
    }

    /**
     * @param int $bookmarkCount
     */
    public function setBookmarkCount($bookmarkCount)
    {
        $this->bookmarkCount = $bookmarkCount;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getSpeciality()
    {
        return $this->speciality;
    }

    /**
     * @param string $speciality
     */
    public function setSpeciality($speciality)
    {
        $this->speciality = $speciality;
    }
}
