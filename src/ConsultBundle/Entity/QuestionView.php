<?php
/**
 * @author Anshuman
 * Date: 29/04/15
 * Time: 13:36
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionViewsRepository")
 * @ORM\Table(name="question_views")
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionView extends BaseEntity
{
    /**
     * @ORM\Column(name="question_id", type="integer")
     */
    protected $question_id;

    /**
     * @ORM\Column(type="integer", name="practo_account_id")
     */
    protected $practoAccountId;

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

    /**
     * Get PractoAccountId
     *
     * @return integer
     */
    public function getPractoAccountId()
    {
        return $this->practoAccountId;
    }

    /**
     * Set PractoAccountId
     *
     * @param integer $practoAccountId - PractoAccountId
     */
    public function setPractoAccountId($practoAccountId)
    {
        $this->setInt('practoAccountId', $practoAccountId);
    }
}
