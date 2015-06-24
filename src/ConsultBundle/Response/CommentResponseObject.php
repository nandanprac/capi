<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 05/06/15
 * Time: 13:15
 */

namespace ConsultBundle\Response;

/**
 * Class CommentResponseObject
 *
 * @package ConsultBundle\Response
 */
class CommentResponseObject extends ConsultResponseObject
{
    /**
     * @var int
     */
    private $practoAccountId;

    /**
     * @var string
     */
    private $numToIdentify;

    private $text;

    private $votes;

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
     * @return mixed
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param mixed $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $this->getInt($votes);
    }

    /**
     * @return int
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * @param int $practoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->practoAccountId = $this->getInt($practoAccountId);
    }

    /**
     * @return string
     */
    public function getNumToIdentify()
    {
        return $this->numToIdentify;
    }

    /**
     * @param string $numToIdentify
     */
    public function setNumToIdentify($numToIdentify)
    {
        $this->numToIdentify = $this->getInt($numToIdentify);
    }
}
