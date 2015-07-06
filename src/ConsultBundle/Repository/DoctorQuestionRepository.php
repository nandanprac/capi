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
                $state = strtoupper($filters['state']);
                $qb->andWhere('dq.state = :state')
                    ->setParameter('state', $state);
            }

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
        }

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

        $qb->select('dq.practoAccountId AS practoAccountId', 'r.id AS id', 'r.text AS text', 'r.rating', 'r.createdAt AS createdAt', 'count(DISTINCT rv.id) AS votes', 'rv1.vote as vote', 'ds.name as name', 'ds.profilePicture as profilePicture', 'ds.location as location', 'ds.fabricDoctorId as doctorId', 'ds.speciality as speciality', 'rf.flagCode as flagCode', 'rf.flagText as flagText')
            ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
            ->innerJoin(ConsultConstants::DOCTOR_SETTING_ENTITY_NAME, 'ds', 'WITH', 'dq.practoAccountId = ds.practoAccountId AND ds.softDeleted = 0 ')
            ->innerJoin(ConsultConstants::DOCTOR_REPLY_ENTITY_NAME, 'r', 'WITH', 'r.doctorQuestion = dq AND r.softDeleted = 0 ')
            ->leftJoin(
                ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY,
                'rv',
                'WITH',
                'rv.reply = r AND rv.softDeleted = 0 AND rv.vote = 1 '
            )
            ->leftJoin(
                ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY,
                'rv1',
                'WITH',
                'r = rv1.reply AND rv1.practoAccountId = :practoAccountId AND rv1.softDeleted = 0 '
            )
            ->leftJoin(
                ConsultConstants::DOCTOR_REPLY_FLAG_ENTITY_NAME,
                'rf',
                'WITH',
                'r = rf.doctorReply AND rf.practoAccountId = :practoAccountId AND rf.softDeleted = 0 '
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



    /**
     * @param \ConsultBundle\Entity\Question $question
     *
     * @return array
     */
    public function findModerationReplies(Question $question)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('dq.practoAccountId AS doctorId', 'r.id AS id', 'r.text AS text', 'r.rating', 'r.createdAt AS createdAt' , 'COALESCE(SUM(rv.vote),0) AS votes')
            ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
            ->innerJoin(ConsultConstants::DOCTOR_REPLY_ENTITY_NAME, 'r', 'WITH', 'r.doctorQuestion = dq AND r.softDeleted = 0 ')
            ->leftJoin(
                ConsultConstants::DOCTOR_REPLY_VOTE_ENTITY,
                'rv',
                'WITH',
                'rv.reply = r AND rv.softDeleted = 0 '
            )

            ->where('dq.question = :question')
            ->andWhere('dq.softDeleted = 0 ')
            ->addGroupBy('r')
            ->addOrderBy('votes', 'DESC');

        $qb->setParameter('question', $question);

//        $qbViewed = $this->_em->createQueryBuilder();
//        $qbViewed = $qb;
//        $qbViewed->andWhere("dq.question.state = 'INVALID'");
//        $viewedCount = count(new Paginator($qbViewed->getQuery(), $fetchJoinCollection= true));
//
//        $qbAnswered = $this->_em->createQueryBuilder();
//        $qbAnswered = $qb;
//        $qbAnswered->andWhere("dq.question.state = 'INVALID'");
//        $answeredCount = count(new Paginator($qbAnswered->getQuery(), $fetchJoinCollection= true));
//
//        $qbRated = $this->_em->createQueryBuilder();
//        $qbRated = $qb;
//        $qbRated->andWhere("dq.question.state = 'INVALID'");
//        $ratedCount = count(new Paginator($qbAnswered->getQuery(), $fetchJoinCollection= true));


//        ,"viewedCount"=>$viewedCount,"answeredCount"=>$answeredCount


        $doctorQuestions = $qb->getQuery()->getArrayResult();
        //var_dump($doctorQuestions);die;

        return $doctorQuestions;
    }

    /**
     * @param int $replyId
     * @param int $practoAccountId
     *
     * @return array
     */
    public function findReplyById($replyId, $practoAccountId = 0)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('dq.practoAccountId AS practoAccountId', 'r.id AS id', 'r.text AS text', 'r.rating', 'r.createdAt AS createdAt', 'COALESCE(SUM(rv.vote),0) AS votes', 'rv1.vote as vote', 'ds.name as name', 'ds.profilePicture as profilePicture', 'ds.location as location', 'ds.fabricDoctorId as doctorId', 'ds.speciality as speciality', 'rf.flagCode as flagCode', 'rf.flagText as flagText')
            ->from(ConsultConstants::DOCTOR_QUESTION_ENTITY_NAME, 'dq')
            ->innerJoin(ConsultConstants::DOCTOR_SETTING_ENTITY_NAME, 'ds', 'WITH', 'dq.practoAccountId = ds.practoAccountId AND ds.softDeleted = 0 ')
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
            ->leftJoin(
                ConsultConstants::DOCTOR_REPLY_FLAG_ENTITY_NAME,
                'rf',
                'WITH',
                'r = rf.doctorReply AND rf.practoAccountId = :practoAccountId AND rf.softDeleted = 0 '
            )
            ->where('r.id = :id')
            ->andWhere('dq.softDeleted = 0 ')
            ->addGroupBy('r')
            ->addOrderBy('votes', 'DESC');

        $qb->setParameter('practoAccountId', $practoAccountId);
        $qb->setParameter('id', $replyId);

        $doctorQuestion = $qb->getQuery()->getArrayResult();

        return $doctorQuestion;
    }
}
