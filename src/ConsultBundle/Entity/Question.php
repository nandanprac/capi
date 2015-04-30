<?php
/**
 * @author Anshuman
 * Date: 29/04/15
 * Time:*/

namespace ConsultBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionRepository")
 * @ORM\Table(name="questions")
 * @ORM\HasLifecycleCallbacks()
 */
class Question extends BaseEntity
{
    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     */
    protected $practo_account_id;

    /**
     * @ORM\Column(length=360, name="question_text")
     */
    protected $questionText;

    /**
     * @ORM\Column(length=5)
     */
    protected $state;

    /**
     * @ORM\Column(type="smallint", name="is_user_anonymous")
     */
    protected $isUserAnonymous;

   /**
    * @ORM\OneToMany(targetEntity="QuestionImage", mappedBy="question")
    */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="DoctorQuestion", mappedBy="question")
     */
    protected $doctorQuestions;

    /**
     * @ORM\OneToMany(targetEntity="QuestionTag", mappedBy="question")
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="QuestionBookmark", mappedBy="question")
     */
    protected $bookmarks;

    /**
     * @ORM\OneToMany(targetEntity="QuestionView", mappedBy="question")
     */
    protected $views;

    /**
     * @ORM\OneToMany(targetEntity="UserNotification", mappedBy="question")
     */
    protected $userNotifications;

    /**
     * @ORM\OneToMany(targetEntity="DoctorNotification", mappedBy="question")
     */
    protected $doctorNotifications;

    /**
     * @ORM\ManyToOne(targetEntity="UserInfo")
     * @ORM\JoinColumn(name="user_info_id", referencedColumnName="id")
     */
    protected $patientInfo;


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
}