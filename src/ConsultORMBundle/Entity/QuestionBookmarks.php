<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 29/04/15
 * Time: 13:35
 */

namespace ConsultORMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ConsultORMBundle\Repository\QuestionBookmarksRepository")
 * @ORM\Table(name=Question_Bookmarks)
 * @ORM\HasLifecycleCallbacks()
 */
class QuestionBookmarks extends ConsultEntity {

}