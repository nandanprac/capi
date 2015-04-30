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
     * @ORM\Column(type="integer")
     */
    protected $user_id;

    /**
     * @ORM\Column(length=360, name="question_text")
     */
    protected $questionText;

    /**
     * @ORM\Column(length=5)
     */
    protected $state;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $isUserAnonymous;

   /**
    * @ORM\OneToMany(targetEntity="QuestionImages", mappedBy="question")
    */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="DoctorQuestions", mappedBy="question")
     */
    protected $doctorQuestions;

    /**
     * @ORM\OneToMany(targetEntity="QuestionTags", mappedBy="question")
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="QuestionBookmarks", mappedBy="question")
     */
    protected $bookmarks;

    /**
     * @ORM\OneToMany(targetEntity="QuestionViews", mappedBy="question")
     */
    protected $views;

    /**
     * @ORM\OneToMany(targetEntity="PatientNotification", mappedBy="question")
     */
    protected $patientNotifications;

    /**
     * @ORM\OneToMany(targetEntity="DoctorNotification", mappedBy="question")
     */
    protected $doctorNotifications;

    /**
     * @ORM\ManyToOne(targetEntity="PatientAdditionalInfo")
     * @ORM\JoinColumn(name="patient_additional_info_id", referencedColumnName="id")
     */
    protected $patientAdditionalInfo;


    public function _construct(){
        $this->images = new ArrayCollection();
        $this->doctorQuestions = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
        $this->views = new ArrayCollection();
        $this->patientNotifications = new ArrayCollection();
        $this->doctorNotifications = new ArrayCollection();
    }



}