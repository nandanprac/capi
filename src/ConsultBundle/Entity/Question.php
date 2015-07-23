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
     * @ORM\ManyToOne(targetEntity="ConsultBundle\Entity\UserInfo", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_info_id", referencedColumnName="id", nullable = false)
     */
    private $userInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $subject;

    /**
     * @ORM\Column(name="text", type="text")
     *
     * @Assert\NotBlank
     */
    private $text;

    /**
     * @var string
     * @ORM\Column(name="speciality", type="string", length=255, nullable=true)
     */
    private $speciality;

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
    * @ORM\OneToMany(targetEntity="QuestionImage", mappedBy="question", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
    * @var ArrayCollection $images
    */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="QuestionComment", mappedBy="question", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     * @var ArrayCollection $comments
     */
    private $comments;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="ConsultBundle\Entity\QuestionView", mappedBy="question", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     */
    private $views;

    /**
     * @ORM\OneToMany(targetEntity="ConsultBundle\Entity\QuestionBookmark", mappedBy="question", fetch="EXTRA_LAZY")
     */
    private $bookmarks;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="ConsultBundle\Entity\DoctorQuestion", mappedBy="question", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     */
    private $doctorQuestions;

    /**
     * @ORM\OneToMany(targetEntity="QuestionTag", mappedBy="question", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     * @var ArrayCollection $tags
     */
     protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="UserNotification", mappedBy="question",fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     * @var ArrayCollection $tags
     */
    protected $patientNotifications;

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
        $this->subject = trim($subject);
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
    public function setViewedAt($viewedAt)
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
        $this->bookmarks = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getDoctorQuestions()
    {
        return $this->doctorQuestions;
    }

    /**
     * @param mixed $doctorQuestions
     */
    public function setDoctorQuestions($doctorQuestions)
    {
        $this->doctorQuestions = $doctorQuestions;
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

    /**
     * @return mixed
     */
    public function getBookmarks()
    {
        return $this->bookmarks;
    }

    /**
     * @param mixed $bookmarks
     */
    public function setBookmarks($bookmarks)
    {
        $this->bookmarks = $bookmarks;
    }


    /**
     * get count of bookmarks
     * @return int
     */
    public function getBookmarkCount()
    {
        return $this->bookmarks->count();
    }

    /**
     * @param \ConsultBundle\Entity\QuestionBookmark $bookmark
     */
    public function addBookmark(QuestionBookmark $bookmark)
    {
        $this->bookmarks->add($bookmark);
    }

    /**
     * Get tags
     *
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }
    /**
     * Add Tag
     *
     * @param QuestionTag $tag - Question Tag
     */
    public function addTag(QuestionTag $tag)
    {
        $this->tags->add($tag);
    }

    /**
     * Clear Question Tags
     */
    public function clearTags()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getPatientNotifications()
    {
        return $this->patientNotifications;
    }

    /**
     * @param ArrayCollection $patientNotifications
     */
    public function setPatientNotifications($patientNotifications)
    {
        $this->patientNotifications = $patientNotifications;
    }

}
