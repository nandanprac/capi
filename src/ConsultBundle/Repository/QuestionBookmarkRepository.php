<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 05/05/15
 * Time: 18:10
 */

namespace ConsultBundle\Repository;

use ConsultBundle\Constants\ConsultConstants;
use Doctrine\ORM\EntityRepository;

class QuestionBookmarkRepository extends EntityRepository{

    public function findBookmark($practoAccountId, $questionId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('b')
           ->from(ConsultConstants::$QUESTION_BOOKMARK_ENTITY_NAME, 'b')
           ->innerJoin(ConsultConstants::$QUESTION_ENTITY_NAME, 'q', 'WITH', 'q = b.question')
           ->where('b.softDeleted = 0')
           ->andWhere('b.practoAccountId = :practoAccountId')
           ->andWhere('q.id = :questionId')
           ->setParameter('practoAccountId', $practoAccountId)
           ->setParameter('questionId', $questionId);

        $questionBookmark = $qb->getQuery()->getResult();

        if (is_null($questionBookmark))
            return null;
        return $questionBookmark;
    }
}
