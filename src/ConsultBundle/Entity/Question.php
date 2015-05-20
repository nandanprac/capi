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
     * @ORM\Column(type="integer", name="practo_account_id")
     *
     * @var integer $practoAccountId
     *
     * @Assert\NotBlank
     */
    protected $practoAccountId;

    /**
     * @ORM\Column(length=360, name="text")
     *
     * @Assert\NotBlank
     */
    protected $text;

    /**
     * @ORM\Column(length=10, name="state")
     *
     * @Assert\Choice(choices = {"NEW", "ASSIGNED", "ANSWERED", "GENERIC", "UNCLASSIFIED"}, message = "Invalid value for state of a question")
     */
    protected $state="NEW";

    /**
     * @ORM\Column(type="smallint", name="user_anonymous")
     */
    protected $userAnonymous=1;

    /**
     * @ORM\Column(type="integer", name="view_count")
     */
    protected $viewCount = 0;

    /**
     * @ORM\Column(type="integer", name="share_count")
     */
    protected $shareCount = 0;

    /**
     * @ORM\Column(type="datetime", name="viewed_at", nullable=true)
     */
    protected $viewedAt;


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
    * @ORM\OneToMany(targetEntity="QuestionImage", mappedBy="question", cascade={"persist", "remove"})
    * @var ArrayCollection $images
    */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="DoctorQuestion", mappedBy="question", cascade={"persist", "remove"})
     * @var ArrayCollection $doctorQuestions
     */
    protected $doctorQuestions;

    /**
     * @ORM\OneToMany(targetEntity="QuestionTag", mappedBy="question", cascade={"persist", "remove"})
     * @var ArrayCollection $tags
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="QuestionBookmark", mappedBy="question", cascade={"persist", "remove"})
     * @var ArrayCollection $bookmarks
     */
    protected $bookmarks;

    /**
     * @ORM\OneToMany(targetEntity="QuestionView", mappedBy="question", cascade={"persist"})
     * @var ArrayCollection $views
     */
    protected $views;

    /**
     * @ORM\OneToMany(targetEntity="UserNotification", mappedBy="question", cascade={"persist", "remove"})
     * @var ArrayCollection $userNotifications
     */
    protected $userNotifications;

    /**
     * @ORM\OneToMany(targetEntity="DoctorNotification", mappedBy="question", cascade={"persist", "remove"})
     * @var ArrayCollection $doctorNotifications
     */
    protected $doctorNotifications;

    /**
     * @ORM\ManyToOne(targetEntity="UserInfo")
     * @ORM\JoinColumn(name="user_info_id", referencedColumnName="id")
     */
    protected $userInfo;

    /**
     * @ORM\OneToMany(targetEntity="QuestionComment", mappedBy="question", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     * @var ArrayCollection $comments
     */
    protected $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->doctorQuestions = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
        $this->views = new ArrayCollection();
        $this->userNotifications = new ArrayCollection();
        $this->doctorNotifications = new ArrayCollection();
        $this->comments = new ArrayCollection();
        //$this->de
    }

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


    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * Get Doctor Questions
     *
     * @return ArrayCollection
     */
    public function getDoctorQuestions()
    {
        return $this->doctorQuestions;
    }

    /**
     * Add Doctor Question
     *
     * @param DoctorQuestion $questionDoctor - Question Doctor
     */
    public function addQuestionDoctor(DoctorQuestion $questionDoctor)
    {
        $this->$doctorQuestions->add($questionDoctor);
    }

    /**
     * Clear Doctor Questions
     */
    public function clearDoctorQuestion()
    {
        $this->$doctorQuestions = new ArrayCollection();
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
     * Get bookmarks
     *
     * @return ArrayCollection
     */
    public function getBookmarks()
    {
        return $this->bookmarks;
    }

    /**
     * Add Bookmark
     *
     * @param QuestionBookmark $bookmark - Question Bookmark
     */
    public function addBookmark(QuestionBookmark $bookmark)
    {
        $this->bookmarks->add($bookmark) ;
    }

    /**
     * Clear Question Bookmarks
     */
    public function clearBookmarks()
    {
        $this->bookmarks = new ArrayCollection();
    }

    /**
     * Get views
     *
     * @return ArrayCollection
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add Views
     *
     * @param QuestionView $view - Question View
     */
    public function addView(QuestionView $view)
    {
        $this->views->add($view);
    }

    /**
     * Clear Question Views
     */
    public function clearViews()
    {
        $this->views = new ArrayCollection();
    }

    /**
     * Get user notification
     *
     * @return ArrayCollection
     */
    public function getUserNotifications()
    {
        return $this->userNotifications;
    }

    /**
     * Add User Notification
     *
     * @param QuestionNotification $userNotification - User Notification
     */
    public function addUserNotification(UserNotification $notification)
    {
        $this->userNotifications->add($notification);
    }

    /**
     * Clear User Notifications
     */
    public function clearUserNotifications()
    {
        $this->userNotifications = new ArrayCollection();
    }

    /**
     * Get Doctor Notifications
     *
     * @return ArrayCollection
     */
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
    {
        $this->doctorNotifications = new ArrayCollection();
    }

    /**
     * Get PractoAccountId
     *
     * @return integer
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * Set PractoAccountId
     *
     * @param integer $practoAccountId - PractoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->setInt('practoAccountId', $practoAccountId);
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
        $this->setString('text', $text);
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
        $this->setString('state', $state);
    }

    /**
     * Is User Anonymous
     *
     * @return boolean
     */
    public function isUserAnonymous()
    {
        return $this->userAnonymous;
    }

    /**
     * Set user anonymous
     *
     * @param boolean $userAnonymous - User Anonymous
     */
    public function setUserAnonymous($userAnonymous)
    {
        $this->setBoolean('userAnonymous', $userAnonymous);
    }

    /**
     * @return integer
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * @param integer
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
     * @param integer
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
     * @param QuestionComment $comments - Question Comment
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
}
