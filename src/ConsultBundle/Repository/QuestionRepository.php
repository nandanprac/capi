<?php

namespace ConsultBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use ConsultBundle\Constants\ConsultConstants;
use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository{

    public function findQuestionsByFilters($practoAccountId, $bookmark, $state, $category, $modifiedAfter, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('q')
           ->from(ConsultConstants::$QUESTION_ENTITY_NAME, 'q')
           ->where('q.softDeleted = 0')
           ->orderBy('q.modifiedAt', 'DESC')
            ->groupBy('q')
           ->setMaxResults($limit)
           ->setFirstResult($offset);

        if (isset($modifiedAfter)) {
            $qb->andWhere('q.modifiedAt > :modifiedAt');
            $qb->setParameter('modifiedAt', $modifiedAfter);
        }

        if (isset($state)) {
            $qb->andWhere('q.state IN(:state)');
            $qb->setParameter('state', $state);
        }

        if (isset($category)) {
            $qb->innerJoin(ConsultConstants::$QUESTION_TAG_ENTITY_NAME, 't', 'WITH', 'q = t.question');
            $qb->andWhere('t.tag IN(:category)');
            $qb->setParameter('category', $category);
        }

        if (isset($practoAccountId)) {
            if (isset($bookmark) and $bookmark == "false"){
                $qb->andWhere('q.practoAccountId = :practoAccountID');
                $qb->setParameter('practoAccountID', $practoAccountId);
            } else if (isset($bookmark) and $bookmark == "true") {
                $qb->innerJoin(ConsultConstants::$QUESTION_BOOKMARK_ENTITY_NAME, 'b', 'WITH', 'q = b.question');
                $qb->andWhere('b.practoAccountId = :practoAccountId');
                $qb->setParameter('practoAccountId', $practoAccountId);
            } else {
                $qb->leftJoin(ConsultConstants::$QUESTION_BOOKMARK_ENTITY_NAME, 'b', 'WITH', 'q = b.question');
                $qb->andWhere('q.practoAccountId = :practoAccountId OR b.practoAccountId = :practoAccountId');
                $qb->setParameter('practoAccountId', $practoAccountId);
            }
        }

        $questionList = $qb->getQuery()->getResult();
        $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
        $count = count($paginator);

        if (is_null($questionList))
            return null;
        return array($questionList, $count);
    }
}
