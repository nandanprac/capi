<?php

namespace ConsultBundle\Repository;

use ConsultBundle\Constants\ConsultConstants;
use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository{

    public function findQuestionsByModifiedTime($modifiedAt)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('q')
           ->from(ConsultConstants::$QUESTION_ENTITY_NAME, 'q')
           ->where('q.modifiedAt > :modifiedAt')
           ->andWhere('q.softDeleted = 0')
           ->setParameter('modifiedAt', $modifiedAt);
           ->orderBy('q.modifiedAt', 'DESC')
        $questionList = $qb->getQuery()->getResult();

        if (is_null($questionList)) {
            return null;
        }

        return $questionList;
    }

    public function findQuestionsByCategory($category, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('q')
           ->from(ConsultConstants::$QUESTION_ENTITY_NAME, 'q')
           ->innerJoin(ConsultConstants::$QUESTION_TAG_ENTITY_NAME, 't', 'WITH', 'q = t.question')
           ->andWhere('t.tag = :category')
           ->andWhere('q.softDeleted = 0')
           ->setParameter('category', $category)
           ->orderBy('q.modifiedAt', 'DESC')
           ->setMaxResults($limit)
           ->setFirstResult($offset);
        $questionList = $qb->getQuery()->getResult();

        if (is_null($questionList)) {
            return null;
        }

        return $questionList;
    }

}
