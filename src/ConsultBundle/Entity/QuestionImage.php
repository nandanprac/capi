<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 13:34
 */

namespace ConsultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionImageRepository")
 * @ORM\Table(name="question_images")
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionImage extends BaseEntity
{
    /**
     * @ORM\Column(name="question_id", type="integer")
     */
    protected $question_id;




    /**
     * @ORM\Column(name="url", type="text", name="url")
     */
    protected $url;



    /**
     * Get Url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set Url
     *
     * @param string $url - URl
     */
    public function setUrl($url)
    {
        $this->setString('url', $url);
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
