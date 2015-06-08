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
        $this->votes = $votes;
    }


}