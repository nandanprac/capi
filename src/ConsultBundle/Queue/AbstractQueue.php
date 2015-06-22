<?php

namespace ConsultBundle\Queue;

/**
 * Abstract Queue
 */
abstract class AbstractQueue
{
    private $queueName;
    private $queuePrefix = '';
    private $consultDomain;

    const PUSH_TEST              = 'push_tester';
    const DAA                    = 'doctor_question_assignment';
    const CONSULT_GCM            = "consult_gcm_push";
    const ASSIGNMENT_UPDATE      = "doctor_assignment_persistance";

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
    public function sendMessage($message, $delay = null)
    {
        if (!($message instanceof Message)) {
            $message = new Message($message);
        }

        $this->doSendMessage($message, $delay);
    }

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
    public function __construct($queuePrefix = '')
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
        $host = $this->consultDomain->getHost();
        $parts = parse_url($host);
        $subdomain = explode('.', $parts['host'])[0];
        $queueName = str_replace('consult', $this->queueName, $subdomain);

        return $this->queuePrefix.$queueName;
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
     * Set Practo Domain
     *
     * @param PractoDomain $consultDomain - Practo Domain
     */
    public function setConsultDomain($consultDomain)
    {
        $this->consultDomain = $consultDomain;
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
            default:
                return 60;
        }
    }

    /**
     * Actual Send Message
     *
     * @param Message $message - Message
     * @param integer $delay   - Delay
     */
    abstract protected function doSendMessage(Message $message, $delay = null);
}
