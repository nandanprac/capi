<?php
/**
 * @author Anshuman
 * Date: 29/04/15
 * Time:*/

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
<<<<<<< HEAD
    * @ORM\OneToMany(targetEntity="QuestionImage", mappedBy="question", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
=======
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
    * @ORM\OneToMany(targetEntity="QuestionImage", mappedBy="question", cascade={"persist", "remove"})
>>>>>>> master
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
     * @ORM\OneToMany(targetEntity="QuestionTag", mappedBy="question", cascade={"persist", "remove"})
     * @var ArrayCollection $tags
     */
     protected $tags;

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

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->doctorQuestions = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

<<<<<<< HEAD
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
=======
>>>>>>> master
    public function getUserInfo()
    {
        return $this->userInfo;
    }

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

<<<<<<< HEAD
    /**
     * @param array $images
     */
=======

>>>>>>> master
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
<<<<<<< HEAD
     * @return string
=======
     * @param QuestionBookmark $bookmark - Question Bookmark
     */
    public function addBookmark(QuestionBookmark $bookmark)
    {
        $this->bookmarks->add($bookmark) ;
    }

    /**
     * Clear Question Bookmarks
>>>>>>> master
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
<<<<<<< HEAD
    public function getComments()
=======
    public function getDoctorNotifications()
    {
        return $this->doctorNotifications;
    }

    /**
     * Add Doctor Notification
     *
     * @param DoctorNotification $notification
     * @internal param DoctorNotification $doctorNotification - Doctor Notification
     */
    public function addDoctorNotification(DoctorNotification $notification)
    {
        $this->doctorNotifications->add($notification);
    }

    /**
     * Clear Doctor Notifications
     */
    public function clearDoctorNotifications()
>>>>>>> master
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

<<<<<<< HEAD
=======
    /**
     * @param integer
     */
    public function setViewCount($count)
    {
        $this->viewCount = $count;
    }
>>>>>>> master

    /**
     * get count of bookmarks
     * @return int
     */
    public function getBookmarkCount()
    {
        return $this->bookmarks->count();
    }

    /**
<<<<<<< HEAD
     * @param \ConsultBundle\Entity\QuestionBookmark $bookmark
=======
     * @param integer
>>>>>>> master
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
<<<<<<< HEAD
     * @param QuestionTag $tag - Question Tag
=======
     * @param QuestionComment $comments - Question Comment
>>>>>>> master
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
<<<<<<< HEAD
=======

    /**
     * Get Details
     *
     * @return ArrayCollection
     */
   /* public function getDetails()
    {
        return $this->details;
    }*/

    /**
     * Add Payment Detail
     *
     * @param PaymentDetail $paymentDetail - Payment Detail
     */
    /*public function addDetail(PaymentDetail $paymentDetail)
    {
        $this->details->add($paymentDetail);
    }*/
>>>>>>> master
}
