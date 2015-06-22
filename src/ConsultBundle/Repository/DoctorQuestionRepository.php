<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 06/05/15
 * Time: 12:05
 */

namespace ConsultBundle\Repository;

<<<<<<< HEAD
use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\Question;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Doctor Question Repository
 */
class DoctorQuestionRepository extends EntityRepository
{
=======

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DoctorQuestionRepository extends EntityRepository{
>>>>>>> master

    /**
     * @param int   $doctorId
     * @param array $filters
     *
     * @return array
     * @throws \Exception
     */
    public function findByFilters($doctorId, $filters)
    {

        $qb = $this->_em->createQueryBuilder();
        $questions = null;
        $modifiedAfter = array_key_exists('modifiedAfter', $filters) ? $filters['modifiedAfter'] : null;
        $limit = array_key_exists('limit', $filters) ? $filters['limit'] : 30;
        $offset = array_key_exists('offset', $filters) ? $filters['offset'] : 0;
<<<<<<< HEAD
        try {
             $qb->select('dq AS doctorQuestion', 'r.rating AS rating', 'count(DISTINCT b.id) AS bookmarkCount', '(count(DISTINCT rv.id) - count(DISTINCT rvn.id)) AS votes')
                ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
                ->leftJoin(ConsultConstants::DOCTOR_REPLY_ENTITY_NAME, 'r', 'WITH', 'r.doctorQuestion = dq AND r.softDeleted = 0')
                ->leftJoin(ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY, 'rv', 'WITH', 'rv.reply = r AND  rv.vote = 1 AND rv.softDeleted = 0')
                ->leftJoin(ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY, 'rvn', 'WITH', 'rvn.reply = r AND rvn.vote = -1 AND rvn.softDeleted = 0')
                ->leftJoin(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME, 'b', 'WITH', 'dq.question = b.question AND b.softDeleted = 0 ')
                ->where('dq.softDeleted = 0')
                ->groupBy('dq.id');

            if ($doctorId != -1) {
                $qb->andWhere(' dq.practoAccountId = :doctorId');
                $qb->setParameter('doctorId', $doctorId);
            }

            if (array_key_exists('state', $filters)) {
=======
        try{
             $qb->select(array('q'))
                ->from("ConsultBundle:Question", 'q')
                ->innerJoin('q.doctorQuestions', 'dq')
                ->where('dq.practoAccountId = :doctorId');

             if (array_key_exists('reject', $filters)) {
               $state = $filters['reject'];
               if (strtolower($state) == 'false'){
                  $qb->andWhere('dq.rejectedAt is NULL');
               } else if (strtolower($state) == 'true'){
                  $qb->andWhere('dq.rejectedAt is not NULL');
               }
             }

             if (array_key_exists('view', $filters)) {
               $state = $filters['view'];
               if (strtolower($state) == 'false'){
                  $qb->andWhere('dq.viewedAt is NULL');
               } else if (strtolower($state) == 'true'){
                  $qb->andWhere('dq.viewedAt is not NULL');
               }
             }

             if (array_key_exists('state', $filters)) {
>>>>>>> master
                $state = strtoupper($filters['state']);
                $qb->andWhere('dq.state = :state')
                  ->setParameter('state', $state);
             }

<<<<<<< HEAD
            if (isset($modifiedAfter)) {
                $qb->andWhere('dq.modifiedAt > :modifiedAt');
                $qb->setParameter('modifiedAt', $modifiedAfter);
            }



            $qb->setFirstResult($offset)
                ->setMaxResults($limit)
                ->addOrderBy('dq.createdAt', 'DESC');
            $questions = $qb->getQuery()->getResult();
            $paginator = new Paginator($qb, $fetchJoinCollection = true);
            $count = count($paginator);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
=======
             $qb->setFirstResult($offset)
               ->setMaxResults($limit)
               ->addOrderBy('q.modifiedAt', 'DESC')
               ->setParameter('doctorId',$doctorId);
             $questions = $qb->getQuery()->getResult();
             $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
             $count = count($paginator);

        } catch( \Exception $e ) {
            return $e->getMessage();
>>>>>>> master
        }

        return array("question"=>$questions, "count"=>$count);
    }


    /**
<<<<<<< HEAD
     * @param integer $doctorId   - Doctor Practo Account Id
     * @param string  $state      - State of Doctor Question Mapping
     * @param integer $maxResults - No. of Max Results
     *
=======
     * @param $doctorId
     * @param $state
     * @param null $maxResults
>>>>>>> master
     * @return array
     */
    public function findDoctorQuestionsForAState($doctorId, $state=null, $maxResults=null)
    {
        $queryStr = "SELECT q FROM ConsultBundle\Entity\Question q join q.doctorQuestions
                        dq WHERE dq.practoAccountId = :doctorId  AND q.softDeleted = 0 AND dq.softDeleted= 0 ";

        if($state != null)
        {
            $queryStr = $queryStr + " AND dq.state = :state";
        }

        $query = $this->_em->createQuery($queryStr);


        $query->setParameter('doctorId', $doctorId);

        if($state != null)
        {$query->setParameter('state', $state);
        }

<<<<<<< HEAD
        if ($maxResults != null) {
            $query->setMaxResults($maxResults);
        }
=======
        if($maxResults!= null)
        $query->setMaxResults($maxResults);
>>>>>>> master

        $questions = $query->getResult();

        return $questions;


    }

<<<<<<< HEAD
    /**
     * @param \ConsultBundle\Entity\Question $question
     * @param int                            $practoAccountId
     *
     * @return array
     */
    public function findRepliesByQuestion(Question $question, $practoAccountId = 0)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('dq.practoAccountId AS doctorId', 'r.id AS id', 'r.text AS text', 'r.rating', 'r.createdAt AS createdAt' , 'COALESCE(SUM(rv.vote),0) AS votes',
            'rv1.vote as vote')
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
        //var_dump($doctorQuestions);die;

        return $doctorQuestions;
    }
=======
>>>>>>> master
}
