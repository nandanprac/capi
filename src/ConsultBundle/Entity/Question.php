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
     */
    protected $text;

    /**
     * @ORM\Column(length=5, name="state")
     */
    protected $state;
    //TODO: put asserts on what states are allowed

    /**
     * @ORM\Column(type="smallint", name="user_anonymous")
     */
    protected $userAnonymous;

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

    public function _construct()
    {
        $this->images = new ArrayCollection();
        $this->doctorQuestions = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
        $this->views = new ArrayCollection();
        $this->userNotifications = new ArrayCollection();
        $this->doctorNotifications = new ArrayCollection();
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
     * @param QuestionDoctor $questionDoctor - Question Doctor
     */
    public function addQuestionDoctor(QuestionDoctor $questionDoctor)
    {
        $this->questionDoctors[] = $questionDoctor;
    }

    /**
     * Clear Doctor Questions
     */
    public function clearDoctorQuestion()
    {
        $this->questionDoctors = new ArrayCollection();
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
        $this->tags[] = $tag;
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
        $this->bookmarks[] = $bookmark;
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
        $this->views[] = $view;
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
        $this->userNotifications[] = $notification;
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
     * @param DoctorNotification $doctorNotification - Doctor Notification
     */
    public function addDoctorNotification(DoctorNotification $notification)
    {
        $this->doctorNotifications[] = $notification;
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
     * Get Details
     *
     * @return ArrayCollection
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Add Payment Detail
     *
     * @param PaymentDetail $paymentDetail - Payment Detail
     */
    public function addDetail(PaymentDetail $paymentDetail)
    {
        $this->details[] = $paymentDetail;
    }
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @var integer
     */
    private $softDeleted;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->doctorQuestions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bookmarks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->views = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userNotifications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->doctorNotifications = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get userAnonymous
     *
     * @return integer 
     */
    public function getUserAnonymous()
    {
        return $this->userAnonymous;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Question
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     * @return Question
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime 
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set softDeleted
     *
     * @param integer $softDeleted
     * @return Question
     */
    public function setSoftDeleted($softDeleted)
    {
        $this->softDeleted = $softDeleted;

        return $this;
    }

    /**
     * Get softDeleted
     *
     * @return integer 
     */
    public function getSoftDeleted()
    {
        return $this->softDeleted;
    }

    /**
     * Remove images
     *
     * @param \ConsultBundle\Entity\QuestionImage $images
     */
    public function removeImage(\ConsultBundle\Entity\QuestionImage $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Add doctorQuestions
     *
     * @param \ConsultBundle\Entity\DoctorQuestion $doctorQuestions
     * @return Question
     */
    public function addDoctorQuestion(\ConsultBundle\Entity\DoctorQuestion $doctorQuestions)
    {
        $this->doctorQuestions[] = $doctorQuestions;

        return $this;
    }

    /**
     * Remove doctorQuestions
     *
     * @param \ConsultBundle\Entity\DoctorQuestion $doctorQuestions
     */
    public function removeDoctorQuestion(\ConsultBundle\Entity\DoctorQuestion $doctorQuestions)
    {
        $this->doctorQuestions->removeElement($doctorQuestions);
    }

    /**
     * Remove tags
     *
     * @param \ConsultBundle\Entity\QuestionTag $tags
     */
    public function removeTag(\ConsultBundle\Entity\QuestionTag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Remove bookmarks
     *
     * @param \ConsultBundle\Entity\QuestionBookmark $bookmarks
     */
    public function removeBookmark(\ConsultBundle\Entity\QuestionBookmark $bookmarks)
    {
        $this->bookmarks->removeElement($bookmarks);
    }

    /**
     * Remove views
     *
     * @param \ConsultBundle\Entity\QuestionView $views
     */
    public function removeView(\ConsultBundle\Entity\QuestionView $views)
    {
        $this->views->removeElement($views);
    }

    /**
     * Remove userNotifications
     *
     * @param \ConsultBundle\Entity\UserNotification $userNotifications
     */
    public function removeUserNotification(\ConsultBundle\Entity\UserNotification $userNotifications)
    {
        $this->userNotifications->removeElement($userNotifications);
    }

    /**
     * Remove doctorNotifications
     *
     * @param \ConsultBundle\Entity\DoctorNotification $doctorNotifications
     */
    public function removeDoctorNotification(\ConsultBundle\Entity\DoctorNotification $doctorNotifications)
    {
        $this->doctorNotifications->removeElement($doctorNotifications);
    }

    /**
     * Set userInfo
     *
     * @param \ConsultBundle\Entity\UserInfo $userInfo
     * @return Question
     */
    public function setUserInfo(\ConsultBundle\Entity\UserInfo $userInfo = null)
    {
        $this->userInfo = $userInfo;

        return $this;
    }

    /**
     * Get userInfo
     *
     * @return \ConsultBundle\Entity\UserInfo 
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }
}
