<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 06/05/15
 * Time: 12:05
 */

namespace ConsultBundle\Repository;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\Question;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Doctor Repository
 */
class DoctorRepository extends EntityRepository
{

    /**
     * @param int   $doctorId
     * @param array $filters
     *
     * @return array
     * @throws \Exception
     */
    public function findByFilters($doctorId, $filters)
    {
        $result = array();
        try {
             $qb = $this->_em->createQueryBuilder();
             $qb->select('sum(rv.vote) as total_votes')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->leftJoin(ConsultConstants::DOCTOR_REPLY_ENTITY_NAME, 'r', 'WITH', 'r.doctorQuestion = dq AND r.softDeleted = 0')
                ->leftJoin(ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY, 'rv', 'WITH', 'rv.reply = r AND r.softDeleted = 0')
                ->where('dq.softDeleted = 0')
                ->andWhere('dq.practoAccountId = :doctorId')
                ->setParameter('doctorId', $doctorId);

             $result = $qb->getQuery()->getArrayResult();
             $result['total_votes'] = intval($result[0]['total_votes']);

             $qb = $this->_em->createQueryBuilder();
             $qb->select('avg(r.rating) as avg_rating')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->leftJoin(ConsultConstants::DOCTOR_REPLY_ENTITY_NAME, 'r', 'WITH', 'r.doctorQuestion = dq AND r.softDeleted = 0')
                ->where('dq.softDeleted = 0')
                ->andWhere('dq.practoAccountId = :doctorId')
                ->setParameter('doctorId', $doctorId);

             $result = $qb->getQuery()->getArrayResult();
             $result['avg_rating'] = floatval($result[0]['avg_rating']);

             $qb = $this->_em->createQueryBuilder();
             $qb->select('sum(q.viewCount) as view_count')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->leftJoin(ConsultConstants::QUESTION_ENTITY_NAME, 'q', 'WITH', 'q = dq.question AND q.softDeleted = 0')
                ->where('dq.softDeleted = 0')
                ->andWhere('dq.practoAccountId = :doctorId')
                ->setParameter('doctorId', $doctorId);

             $result = $qb->getQuery()->getArrayResult();
             $result['view_count'] = intval($result[0]['view_count']);

             $qb = $this->_em->createQueryBuilder();
             $qb->select('count(dn.id) as notification_count')
                ->from(ConsultConstants::DOCTOR_NOTIFICATION_ENTITY_NAME, 'dn')
                ->where('dn.softDeleted = 0')
                ->andWhere('dn.practoAccountId = :doctorId')
                ->setParameter('doctorId', $doctorId);

             $result = $qb->getQuery()->getArrayResult();
             $result['notification_count'] = intval($result[0]['notification_count']);

             $qb = $this->_em->createQueryBuilder();
             $qb->select('count(dq.id) as answered_count')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->where('dq.softDeleted = 0')
                ->andWhere('dq.practoAccountId = :doctorId')
                ->andWhere('dq.state = :state')
                ->setParameter('doctorId', $doctorId)
                ->setParameter('state', "ANSWERED");

             $result = $qb->getQuery()->getArrayResult();
             $result['answered_count'] = intval($result[0]['answered_count']);

             $qb = $this->_em->createQueryBuilder();
             $qb->select('count(dq.id) as assigned_count')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->leftJoin(ConsultConstants::QUESTION_ENTITY_NAME, 'q', 'WITH', 'dq.question = q AND q.softDeleted = 0')
                ->where('dq.softDeleted = 0')
                ->andWhere('dq.practoAccountId = :doctorId')
                ->andWhere('q.state = :state')
                ->setParameter('doctorId', $doctorId)
                ->setParameter('state', "ASSIGNED");

             $result = $qb->getQuery()->getArrayResult();
             $result['assigned_count'] = intval($result[0]['assigned_count']);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $result;
    }
}
