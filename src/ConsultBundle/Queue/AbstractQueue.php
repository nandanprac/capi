<?php

namespace ConsultBundle\Queue;

/**
 * Abstract Queue
 */
abstract class AbstractQueue
{
    private $queueName;
    private $queuePrefix = '';
    private $fabricDomain;

    const PUSH_TEST             = "testing_queue_queue";

    /**
     * Receive Message
     *
     * @return Message
     */
    abstract public function receiveMessage();

    /**
     * Send Message
     *
     * @param mixed   $message - Message
     * @param integer $delay   - Delay
     */
    public function sendMessage($message, $delay=null)
    {
        if (!($message instanceof Message)) {
            $message = new Message($message);
        }

        $this->doSendMessage($message, $delay);
    }

    /**
     * Actual Send Message
     *
     * @param Message $message - Message
     * @param integer $delay   - Delay
     */
    abstract protected function doSendMessage(Message $message, $delay=null);

    /**
     * Delete Message
     *
     * @param Message $message - Message
     */
    abstract public function deleteMessage($message);

    /**
     * Constructor
     *
     * @param string $queuePrefix - Queue Prefix
     */
    public function __construct($queuePrefix='')
    {
        $this->queuePrefix = $queuePrefix;
    }

    /**
     * Get Queue Name
     *
     * @return string
     */
    public function getQueueName()
    {
        $host = $this->fabricDomain->getHost();
        $parts = parse_url($host);
        $subdomain = explode('.', $parts['host'])[0];
        $queueName = str_replace('www', $this->queueName, $subdomain);

        return $this->queuePrefix . $queueName;
    }

    /**
     * Set Queue Name
     *
     * @param string $queueName - Queue Name
     *
     * @return AbstractQueue
     */
    public function setQueueName($queueName)
    {
        $this->queueName = $queueName;

        return $this;
    }

    /**
     * Set Fabric Domain
     *
     * @param FabricDomain $fabricDomain - Fabric Domain
     */
    public function setFabricDomain($fabricDomain)
    {
        $this->fabricDomain = $fabricDomain;
    }

    /**
     * Get Fabric Domain
     *
     * @return FabricDomain
     */
    public function getFabricDomain()
    {
        return $this->fabricDomain;
    }

    /**
     * Get Visibility Timeout
     *
     * @return integer
     */
    public function getVisibilityTimeout()
    {
        switch ($this->queueName) {
            case self::PUSH_TEST:
                return 30;
            default:
                return 300;
        }
    }
}
