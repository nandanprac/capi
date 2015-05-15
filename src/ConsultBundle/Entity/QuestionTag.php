<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 13:35
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionTagRepository")
 * @ORM\Table(name="question_tags")
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionTag extends BaseEntity
{
    /**
     * @ORM\Column(name="question_id", type="integer")
     */
    protected $question_id;

    /**
     * @ORM\Column(type="string", length=127, name="tag")
     */    
    protected $tag;

    /**
     * @ORM\Column(type="smallint", name="user_defined")
     */
    protected $userDefined = 0;



    /**
     * Get Tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set Tag
     *
     * @param string $tag - Tag
     */
    public function setTag($tag)
    {
        $this->setString('tag', $tag);
    }

    /**
     * Is User Defined
     *
     * @return boolean
     */
    public function isUserDefined()
    {
        return $this->userDefined;
    }

    /**
     * Set User Defined
     *
     * @param boolean $userDefined - User Defined
     */
    public function setUserDefined($userDefined)
    {
        $this->setBoolean('userDefined', $userDefined);
    }
    /**
     * @param $question_id
     */
    public function setQuestionId($question_id)
    {
        $this->question_id = $question_id;
    }

    /**
     * @return mixed
     */
    public function getQuestionId()
    {
        return $this->question_id;
    }
}
