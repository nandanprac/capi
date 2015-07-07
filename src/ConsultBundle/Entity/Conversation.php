<?php

namespace ConsultBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity
 * @ORM\Table(name="conversations")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Conversation extends BaseEntity
{

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=32)
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @Exclude()
     * @ORM\ManyToOne(targetEntity = "PrivateThread", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name = "private_thread_id", referencedColumnName = "id")
     */
    private $privateThread;

    /**
     * @var bool
     * @ORM\Column(name="is_doctor_reply", type="boolean")
     */
    private $isDocReply = false;

    /**
     * @ORM\OneToMany(targetEntity="ConsultBundle\Entity\ConversationImage", mappedBy="conversation", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     * @var ArrayCollection $images
     */
    private $images;

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
     * @return boolean
     */
    public function getIsDocReply()
    {
        return $this->isDocReply;
    }

    /**
     * @param boolean $isDocReply
     */
    public function setIsDocReply($isDocReply)
    {
        $this->isDocReply = $isDocReply;
    }

    /**
     * Set Private Thread
     *
     * @param PrivateThread $privateThread
     */
    public function setPrivateThread($privateThread)
    {
        $this->privateThread = $privateThread;
    }

    /**
     * Get Private Thread
     *
     * @return Question
     */
    public function getPrivateThread()
    {
        return $this->privateThread;
    }

    /**
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }



}
