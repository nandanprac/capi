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
 * @ORM\Entity(repositoryClass="ConsultBundle\Repository\QuestionBookmarksRepository")
 * @ORM\Table(name="question_bookmarks")
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionBookmarks extends BaseEntity {

    /**
     * @ORM\ManyToOne(targetEntity = "Question", inversedBy ="bookmarks")
     * @ORM\JoinColumn(name = "question_id", referencedColumnName = "id")
     */
    protected $questions;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;



}