<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 02/07/15
 * Time: 18:17
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity
 * @ORM\Table(name="conversation_images")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ExclusionPolicy("all")
 */
class ConversationImage extends BaseEntity
{

    /**
     *
     * @ORM\ManyToOne(targetEntity = "Conversation", inversedBy="images")
     * @ORM\JoinColumn(name = "conversation_id", referencedColumnName = "id")
     */
    private $conversation;

    /**
     * @ORM\Column(name="url", type="text", name="url")
     * @Expose()
     */
    private $url;

    /**
     * @return mixed
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * @param mixed $conversation
     */
    public function setConversation($conversation)
    {
        $this->conversation = $conversation;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}

