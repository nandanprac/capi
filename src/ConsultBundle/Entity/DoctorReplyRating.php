<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 15:49
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\DoctorReplyRatingRepository")
 * @ORM\Table(name="doctor_reply_ratings")
 */
class DoctorReplyRating extends BaseEntity
{
    /**
     * @ORM\Column(name="practo_account_id", type="integer")
     */
    protected $practoAccountId;

    /**
     * @ORM\ManyToOne(targetEntity="DoctorReply", inversedBy="ratings")
     * @ORM\JoinColumn(name="doctor_reply_id", referencedColumnName="id")
     */
    protected $doctorReply;

    /**
     * @ORM\Column(type="smallint", name="rating")
     */
    protected $rating;
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
     * Set practoAccountId
     *
     * @param integer $practoAccountId
     * @return DoctorReplyRating
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $practoAccountId;

        return $this;
    }

    /**
     * Get practoAccountId
     *
     * @return integer 
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return DoctorReplyRating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
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
     * @return DoctorReplyRating
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
     * @return DoctorReplyRating
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
     * @return DoctorReplyRating
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
     * Set doctorReply
     *
     * @param \ConsultBundle\Entity\DoctorReply $doctorReply
     * @return DoctorReplyRating
     */
    public function setDoctorReply(\ConsultBundle\Entity\DoctorReply $doctorReply = null)
    {
        $this->doctorReply = $doctorReply;

        return $this;
    }

    /**
     * Get doctorReply
     *
     * @return \ConsultBundle\Entity\DoctorReply 
     */
    public function getDoctorReply()
    {
        return $this->doctorReply;
    }
}
