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
 * Doctor Question Repository
 */
class DoctorQuestionRepository extends EntityRepository
{

    /**
     * @param integer $doctorId - Practo Account Id of doctor
     * @param array   $filters  - Filters to find questions assigned to doctors
     *
     * @return array
     */
    public function findByFilters($doctorId, $filters)
	{
        $qb = $this->_em->createQueryBuilder();
        $questions = null;
        $bookmark = array_key_exists('bookmark', $filters) ? $filters['bookmark'] : null;
        $state = array_key_exists('state', $filters) ? $filters['state'] : null;
        $modifiedAfter = array_key_exists('modifiedAfter', $filters) ? $filters['modifiedAfter'] : null;
        $limit = array_key_exists('limit', $filters) ? $filters['limit'] : 500;
        $offset = array_key_exists('offset', $filters) ? $filters['offset'] : 0;
        try {
            //$qb->select('q.text as text', 'q.subject as subject', 'q.viewCount as view_count', 'q.shareCount AS share_count','dq.id AS id', 'dq.state AS state', 'count(b.id) AS bookmarkCount', 'dq.viewedAt as viewed_at', 'dq.createdAt AS created_at')
            $qb->select('q AS question', 'count(b.id) AS bookmarkCount')
               ->from(ConsultConstants::QUESTION_ENTITY_NAME, 'q')
               ->innerJoin(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq', 'WITH', 'q = dq.question')
               ->leftJoin(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME, 'b', 'WITH', 'q = b.question AND b.softDeleted = 0 ')
			   ->where('dq.softDeleted = 0');

			if (isset($modifiedAfter)) {
				$qb->andWhere('dq.modifiedAt > :modifiedAt');
				$qb->setParameter('modifiedAt', $modifiedAfter);
			}

			if ($doctorId != -1) {
				$qb->andWhere(' dq.practoAccountId = :doctorId');
				$qb->setParameter('doctorId', $doctorId);
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
			   ->addOrderBy('dq.createdAt', 'DESC');
             $questions = $qb->getQuery()->getResult();
             $paginator = new Paginator($qb, $fetchJoinCollection = true);
			 $count = count($paginator);
        } catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		var_dump($questions[1]['bookmarkCount']);die;
        return array("question"=>$questions, "count"=>$count);
    }


    /**
     * @param integer $doctorId   - Doctor Practo Account Id
     * @param string  $state      - State of Doctor Question Mapping
     * @param integer $maxResults - No. of Max Results
     *
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

        if ($maxResults != null) {
            $query->setMaxResults($maxResults);
        }

        $questions = $query->getResult();

        return $questions;


    }

    /**
     * @param \ConsultBundle\Entity\Question $question
     * @param int                            $practoAccountId
     *
     * @return array
     */
    public function findRepliesByQuestion(Question $question, $practoAccountId = 0)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('dq.practoAccountId AS doctorId', 'r.id AS id', 'r.text AS text', 'r.rating', 'r.createdAt AS createdAt' , 'COALESCE(SUM(rv.vote),0) AS votes', 'rv1.vote as vote')
            ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
            ->innerJoin(ConsultConstants::DOCTOR_REPLY_ENTITY_NAME, 'r', 'WITH', 'r.doctorQuestion = dq AND r.softDeleted = 0 ')
            ->leftJoin(
                ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY,
                'rv',
                'WITH',
                'rv.reply = r AND rv.softDeleted = 0 '
            )
            ->leftJoin(
                ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY,
                'rv1',
                'WITH',
                'r = rv1.reply AND rv1.practoAccountId= :practoAccountId AND rv1.softDeleted = 0 '
            )
            ->where('dq.question = :question')
            ->andWhere('dq.softDeleted = 0 ')
            ->addGroupBy('r')
            ->addOrderBy('votes', 'DESC');

        $qb->setParameter('practoAccountId', $practoAccountId);
        $qb->setParameter('question', $question);

        $doctorQuestions = $qb->getQuery()->getArrayResult();

        return $doctorQuestions;
    }
}
