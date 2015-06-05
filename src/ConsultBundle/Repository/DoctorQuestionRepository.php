<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 06/05/15
 * Time: 12:05
 */

namespace ConsultBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DoctorQuestionRepository extends EntityRepository
{

    /**
     * @param $doctorId
     * @param $state
     * @return array
     */
    public function findByFilters($doctorId, $filters)
    {
		$qb = $this->_em->createQueryBuilder();
        $questions = null;
        $limit = array_key_exists('limit', $filters) ? $filters['limit'] : 500;
        $offset = array_key_exists('offset', $filters) ? $filters['offset'] : 0;
        try {
            $qb->select(array('q'))
               ->from("ConsultBundle:Question", 'q')
			   ->innerJoin('q.doctorQuestions', 'dq')
			   ->where('dq.softDeleted = 0');

			if ($doctorId != -1) {
				$qb->andWhere('dq.practoAccountId = :doctorId')
                   ->setParameter('doctorId', $doctorId);
			}

            if (array_key_exists('reject', $filters)) {
                $state = $filters['reject'];
                if (strtolower($state) == 'false') {
                    $qb->andWhere('dq.rejectedAt is NULL');
                } elseif (strtolower($state) == 'true') {
                    $qb->andWhere('dq.rejectedAt is not NULL');
                }
            }

            if (array_key_exists('view', $filters)) {
                $state = $filters['view'];
                if (strtolower($state) == 'false') {
                    $qb->andWhere('dq.viewedAt is NULL');
                } elseif (strtolower($state) == 'true') {
                    $qb->andWhere('dq.viewedAt is not NULL');
                }
            }

            if (array_key_exists('state', $filters)) {
                $state = strtoupper($filters['state']);
                $qb->andWhere('dq.state = :state')
                    ->setParameter('state', $state);
            }

             $qb->setFirstResult($offset)
                 ->setMaxResults($limit)
                 ->addOrderBy('q.modifiedAt', 'DESC');
             $questions = $qb->getQuery()->getResult();
             $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
             $count = count($paginator);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return array($questions, $count);

    }


    /**
     * @param $doctorId
     * @param $state
     * @param null     $maxResults
     * @return array
     */
    public function findDoctorQuestionsForAState($doctorId, $state = null, $maxResults = null)
    {
        $queryStr = "SELECT q FROM ConsultBundle\Entity\Question q join q.doctorQuestions
                        dq WHERE dq.practoAccountId = :doctorId  AND q.softDeleted = 0 AND dq.softDeleted= 0 ";

        if ($state != null) {
            $queryStr = $queryStr + " AND dq.state = :state";
        }

        $query = $this->_em->createQuery($queryStr);


        $query->setParameter('doctorId', $doctorId);

        if ($state != null) {
            $query->setParameter('state', $state);
        }

        if ($maxResults!= null) {
            $query->setMaxResults($maxResults);
        }

        $questions = $query->getResult();

        return $questions;


    }
}
