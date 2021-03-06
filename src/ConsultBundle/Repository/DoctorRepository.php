<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 06/05/15
 * Time: 12:05
 */

namespace ConsultBundle\Repository;

use ConsultBundle\Constants\ConsultConstants;
use Doctrine\ORM\EntityRepository;

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
        $results = array();
        $qb = $this->_em->createQueryBuilder();
        try {
            $qb = $this->_em->createQueryBuilder();
            $qb->select('sum(rv.vote) as total_votes')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->leftJoin(ConsultConstants::DOCTOR_REPLY_ENTITY_NAME, 'r', 'WITH', 'r.doctorQuestion = dq AND r.softDeleted = 0')
                ->leftJoin(ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY, 'rv', 'WITH', 'rv.reply = r AND rv.softDeleted = 0')
                ->where('dq.softDeleted = 0')
                ->andWhere('dq.practoAccountId = :doctorId')
                ->setParameter('doctorId', $doctorId);

            $result = $qb->getQuery()->getArrayResult();
            if ($result != null) {
                $results['total_votes'] = intval($result[0]['total_votes']);
            } else {
                $results['total_votes'] = 0;
            }

            $qb = $this->_em->createQueryBuilder();
            $qb->select('avg(r.rating) as avg_rating')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->innerJoin(ConsultConstants::DOCTOR_REPLY_ENTITY_NAME, 'r', 'WITH', 'r.doctorQuestion = dq AND r.softDeleted = 0')
                ->where('dq.softDeleted = 0')
                ->andWhere('dq.practoAccountId = :doctorId')
                ->setParameter('doctorId', $doctorId);

            $result = $qb->getQuery()->getArrayResult();
            if ($result != null) {
                $results['avg_rating'] = round(floatval($result[0]['avg_rating']), 1);
            } else {
                $results['avg_rating'] = 0.0;
            }

            $qb = $this->_em->createQueryBuilder();
            $qb->select('sum(q.viewCount) as view_count')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->innerJoin(ConsultConstants::QUESTION_ENTITY_NAME, 'q', 'WITH', 'q = dq.question AND q.softDeleted = 0')
                ->where('dq.softDeleted = 0')
                ->andWhere('dq.practoAccountId = :doctorId')
                ->andWhere('dq.state = :state')
                ->setParameter('doctorId', $doctorId)
                ->setParameter('state', 'ANSWERED');

            $result = $qb->getQuery()->getArrayResult();
            if ($result != null) {
                $results['view_count'] = intval($result[0]['view_count']);
            } else {
                $results['view_count'] = 0;
            }

            $qb = $this->_em->createQueryBuilder();
            $qb->select('count(distinct dn.id) as notification_count')
                ->from(ConsultConstants::DOCTOR_NOTIFICATION_ENTITY_NAME, 'dn')
                ->where('dn.softDeleted = 0')
                ->andWhere('dn.viewed = 0')
                ->andWhere('dn.practoAccountId = :doctorId')
                ->setParameter('doctorId', $doctorId);

            $result = $qb->getQuery()->getArrayResult();
            if ($result != null) {
                $results['notification_count'] = intval($result[0]['notification_count']);
            } else {
                $results['notification_count'] = 0;
            }

            $qb = $this->_em->createQueryBuilder();
            $qb->select('count(dq.id) as answered_count')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->where('dq.softDeleted = 0')
                ->andWhere('dq.practoAccountId = :doctorId')
                ->andWhere('dq.state = :state')
                ->setParameter('doctorId', $doctorId)
                ->setParameter('state', "ANSWERED");

            $result = $qb->getQuery()->getArrayResult();
            if ($result != null) {
                $results['answered_count'] = intval($result[0]['answered_count']);
            } else {
                $results['answered_count'] = 0;
            }

            $qb = $this->_em->createQueryBuilder();
            $qb->select('count(dq.id) as assigned_count')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->where('dq.softDeleted = 0')
                ->andWhere('dq.practoAccountId = :doctorId')
                ->andWhere('dq.state = :state')
                ->setParameter('doctorId', $doctorId)
                ->setParameter('state', "UNANSWERED");

            $result = $qb->getQuery()->getArrayResult();
            if ($result != null) {
                $results['assigned_count'] = intval($result[0]['assigned_count']);
            } else {
                $results['assigned_count'] = 0;
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $results;
    }

    /**
     * @param string $city
     * @param string $speciality
     *
     * @return array
     * @throws \Exception
     */
    public function findBySpecialityAndCity($city, $speciality)
    {
        $doctorIds = array();
        $curdate = new \DateTime();
        $curdate->setTime(0, 0);
        $city = strtoupper($city);
        $speciality = strtoupper($speciality);
        $qb = $this->_em->createQueryBuilder();
        $qb2= $this->_em->createQueryBuilder();


        try {
            $qb->select('dcs.name as doctorName', 'dcs.practoAccountId as doctorId', 'count(dq.id) as givenQuestions', 'dcs.numQuesDay as questionPerDay')
                ->from(ConsultConstants::DOCTOR_SETTING_ENTITY_NAME, 'dcs')
                ->leftJoin(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq', 'WITH', 'dq.practoAccountId = dcs.practoAccountId AND dq.createdAt > :curdate')
                ->add(
                    'where',
                    $qb->expr()->orX(
                        $qb->expr()->eq('upper(dcs.speciality)', ':speciality'),
                        $qb->expr()->in(
                            'upper(dcs.speciality)',
                            $qb2->select('upper(sub_spc.subSpeciality)')
                                ->from(ConsultConstants::SPECIALITY_ENTITY_NAME, 'spc')
                                ->innerJoin(ConsultConstants::SUB_SPECIALITY_ENTITY_NAME, 'sub_spc', 'WITH', 'sub_spc.speciality = spc and sub_spc.softDeleted = 0')
                                ->where('spc.softDeleted = 0')
                                ->andWhere('upper(spc.name) = :speciality')
                                ->getDQL()
                        )
                    )
                )
                ->andWhere('dcs.activated = 1')
                ->andWhere('dcs.consentGiven = 1');

            if (!empty($city)) {
                $qb->andWhere('upper(dcs.location) = :city')
                    ->setParameter('city', $city);
            }

            $qb->groupBy('dcs.practoAccountId')
                ->having('givenQuestions < dcs.numQuesDay OR dcs.numQuesDay is null')
                ->setParameter('curdate', $curdate)
                ->setParameter('speciality', $speciality);

            $doctors = $qb->getQuery()->getArrayResult();
            if (empty($doctors) && !empty($city)) {
                $doctors = $this->findBySpecialityAndCity(null, $speciality);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        foreach ($doctors as $doctor) {
            array_push($doctorIds, $doctor['doctorId']);
        }

        return $doctorIds;
    }
}
