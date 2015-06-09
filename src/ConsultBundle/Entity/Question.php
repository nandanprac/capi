<?php
/**
 * @author Anshuman
 * Date: 29/04/15
 * Time:
*/

namespace ConsultBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionRepository")
 * @ORM\Table(name="questions")
 * @ORM\HasLifecycleCallbacks()
 */
class Question extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="ConsultBundle\Entity\UserInfo")
     * @ORM\JoinColumn(name="user_info_id", referencedColumnName="id")
     */
    private $userInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=32, nullable=true)
     * @Assert\NotBlank()
     */
    private $subject;

    /**
     * @ORM\Column(length=360, name="text")
     *
     * @Assert\NotBlank
     */
    private $text;

    /**
     * @var string
     * @ORM\Column(name="specialty", type="string", length=255, nullable=true)
     */
    private $specialty;

    /**
     * @ORM\Column(length=20, name="state")
     *
     * @Assert\Choice(choices = {"NEW", "ASSIGNED", "DOCNOTFOUND", "MISMATCH", "ANSWERED", "GENERIC", "UNCLASSIFIED"}, message = "Invalid value for state of a question")
     */
    private $state="NEW";


    /**
     * @ORM\Column(type="integer", name="view_count")
     */
    private $viewCount = 0;

    /**
     * @ORM\Column(type="integer", name="share_count")
     */
    private $shareCount = 0;

    /**
     * @ORM\Column(type="datetime", name="viewed_at", nullable=true)
     */
    private $viewedAt;


    /**
    * @ORM\OneToMany(targetEntity="QuestionImage", mappedBy="question", cascade={"persist", "remove"})
    * @var ArrayCollection $images
    */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="QuestionComment", mappedBy="question", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     * @var ArrayCollection $comments
     */
    private $comments;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="ConsultBundle\Entity\QuestionView", mappedBy="question", cascade={"persist", "remove"})
     */
    private $views;


    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }


    /**
     * @return mixed
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    /**
     * @param mixed $viewedAt
     */
    public function setViewedAt(\DateTime $viewedAt)
    {
        $this->viewedAt = $viewedAt;
    }

    /**
     * constructor
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->doctorQuestions = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * get User object
     * @return User
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Set UserInfo object
     * @param UserInfo $userInfo - UserInfo object
     */
    public function setUserInfo(UserInfo $userInfo)
    {
        $this->userInfo = $userInfo;
    }

    /**
     * Get Images
     *
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add Images
     *
     * @param QuestionImage $image - Question Image
     */
    public function addImage(QuestionImage $image)
    {
        $this->images[] = $image;
    }

    /**
     * Clear Question Images
     */
    public function clearImages()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * @param array $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }


    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set Text
     *
     * @param string $text - Text
     */
    public function setText($text)
    {
        $this->text = trim($text);
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set State
     *
     * @param string $state - State
     */
    public function setState($state)
    {
        $this->state = trim($state);
    }


    /**
     * @return integer
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * @param integer $count - view count of the question
     */
    public function setViewCount($count)
    {
        $this->viewCount = $count;
    }

    /**
     * @return integer
     */
    public function getShareCount()
    {
        return $this->shareCount;
    }

    /**
     * @param integer $count - share count of the question
     */
    public function setShareCount($count)
    {
        $this->shareCount = $count;
    }

    /**
     * Get comments
     *
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add Tag
     *
     * @param QuestionComment $comment - Question Comment
     */
    public function addComment(QuestionComment $comment)
    {
        $this->comments->add($comment);
    }

    /**
     * Clear Question Comments
     */
    public function clearComments()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getSpecialty()
    {
        return $this->specialty;
    }

    /**
     * @param string $specialty
     */
    public function setSpecialty($specialty)
    {
        $this->specialty = $specialty;
    }

    /**
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param array $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @param \ConsultBundle\Entity\QuestionView $view
     */
    public function addViews(QuestionView $view)
    {
        $this->views[] = $view;
    }
}
