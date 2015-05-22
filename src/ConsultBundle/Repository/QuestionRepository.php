<?php

namespace ConsultBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use ConsultBundle\Constants\ConsultConstants;
use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository{

    public function findAllQuestions($modifiedAfter, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('q')
           ->from(ConsultConstants::$QUESTION_ENTITY_NAME, 'q')
           ->where('q.softDeleted = 0')
           ->orderBy('q.modifiedAt', 'DESC')
           ->setMaxResults($limit)
           ->setFirstResult($offset);

        if (isset($modifiedAfter)) {
            $qb->andWhere('q.modifiedAt > :modifiedAt');
            $qb->setParameter('modifiedAt', $modifiedAfter);
        }

        $questionList = $qb->getQuery()->getResult();
        $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
        $count = count($paginator);

        if (is_null($questionList))
            return null;
        return array($questionList, $count);
    }

    public function findQuestionsByAccID($practoAccountId, $modifiedAfter, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('q')
           ->from(ConsultConstants::$QUESTION_ENTITY_NAME, 'q')
           ->where('q.softDeleted = 0')
           ->andWhere('q.practoAccountId = :practoAccID')
           ->setParameter('practoAccID', $practoAccountId)
           ->orderBy('q.modifiedAt', 'DESC')
           ->setMaxResults($limit)
           ->setFirstResult($offset);

        if (isset($modifiedAfter)) {
            $qb->andWhere('q.modifiedAt > :modifiedAt');
            $qb->setParameter('modifiedAt', $modifiedAfter);
        }

        $questionList = $qb->getQuery()->getResult();
        $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
        $count = count($paginator);

        if (is_null($questionList))
            return null;
        return array($questionList, $count);
    }

    public function findBookmarksByAccID($practoAccountId, $modifiedAfter, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('q')
           ->from(ConsultConstants::$QUESTION_BOOKMARK_ENTITY_NAME, 'q')
           ->where('q.softDeleted = 0')
           ->andWhere('q.practoAccountId = :practoAccID')
           ->setParameter('practoAccID', $practoAccountId)
           ->orderBy('q.modifiedAt', 'DESC')
           ->setMaxResults($limit)
           ->setFirstResult($offset);

        if (isset($modifiedAfter)) {
            $qb->andWhere('q.modifiedAt > :modifiedAt');
            $qb->setParameter('modifiedAt', $modifiedAfter);
        }

        $questionList = $qb->getQuery()->getResult();
        $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
        $count = count($paginator);

        if (is_null($questionList))
            return null;
        return array($questionList, $count);
    }

    public function findQuestionsByState($state, $modifiedAfter, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('q')
           ->from(ConsultConstants::$QUESTION_ENTITY_NAME, 'q')
           ->where('q.state = :state')
           ->andWhere('q.softDeleted = 0')
           ->setParameter('state', $state)
           ->orderBy('q.modifiedAt', 'DESC')
           ->setMaxResults($limit)
           ->setFirstResult($offset);

        if (isset($modifiedAfter)) {
            $qb->andWhere('q.modifiedAt > :modifiedAt');
            $qb->setParameter('modifiedAt', $modifiedAfter);
        }

        $questionList = $qb->getQuery()->getResult();
        $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
        $count = count($paginator);

        if (is_null($questionList))
            return null;
        return array($questionList, $count);
    }

    public function findQuestionsByCategory($category, $modifiedAfter, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('q')
           ->from(ConsultConstants::$QUESTION_ENTITY_NAME, 'q')
           ->innerJoin(ConsultConstants::$QUESTION_TAG_ENTITY_NAME, 't', 'WITH', 'q = t.question')
           ->andWhere('t.tag IN(:category)')
           ->andWhere('q.softDeleted = 0')
           ->setParameter('category', $category)
           ->orderBy('q.modifiedAt', 'DESC')
           ->setMaxResults($limit)
           ->setFirstResult($offset);

        if (isset($modifiedAfter)) {
            $qb->andWhere('q.modifiedAt > :modifiedAt');
            $qb->setParameter('modifiedAt', $modifiedAfter);
        }

        $questionList = $qb->getQuery()->getResult();
        $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
        $count = count($paginator);

        if (is_null($questionList))
            return null;
        return array($questionList, $count);
    }

}
