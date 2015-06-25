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
        $qb = $this->_em->createQueryBuilder();
        try {
             $qb->select('sum(rv.vote) as total_votes')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->leftJoin(ConsultConstants::DOCTOR_REPLY_ENTITY_NAME, 'r', 'WITH', 'r.doctorQuestion = dq AND r.softDeleted = 0')
                ->leftJoin(ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY, 'rv', 'WITH', 'rv.reply = r AND r.softDeleted = 0')
                ->where('dq.softDeleted = 0')
				->andWhere('dq.practoAccountId = :doctorId')
				->setParameter('doctorId', $doctorId);

			 $total_votes = $qb->getQuery()->getArrayResult();
			 $result['total_votes'] = $total_votes[0]['total_votes'];
		} catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $result;
    }
}
