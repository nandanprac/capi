<?php

namespace ConsultBundle\Repository;

use ConsultBundle\Entity\Question;
use ConsultBundle\Utility\Utility;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use ConsultBundle\Constants\ConsultConstants;
use Doctrine\ORM\EntityRepository;

/**
 * Question Repository
 */
class QuestionRepository extends EntityRepository
{
    const SELECT_BASIC_QUESTION_QUERY = " SELECT question.id as id, subject as subject, text as text,
 speciality as speciality, view_count as view_count, share_count as share_count, question.viewed_at as viewed_at,
 question.created_at as created_at,
 question.modified_at as modified_at,COUNT(question_bookmarks.id) as bookmark_count
 FROM questions question
 LEFT OUTER JOIN question_bookmarks on question.id = question_bookmarks.question_id
                 AND question_bookmarks.soft_deleted = 0
 WHERE question.soft_deleted = 0
 GROUP BY question.id ";

    /**
     * @param integer   $practoAccountId - User's id
     * @param bool      $bookmark        - require bookmark value
     * @param string    $state           - state of the question
     * @param string    $category        - category of question
     * @param \DateTime $modifiedAfter   - time for the filter
     * @param int       $limit           - limit
     * @param integer   $offset          - offset
     * @return array (Question,count) - list of question objects and the count
     */
    public function findQuestionsByFilters($practoAccountId, $bookmark, $state, $category, $modifiedAfter, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('q as question', 'count(b.id) AS bookmarkCount')
            ->from(ConsultConstants::QUESTION_ENTITY_NAME, 'q')
            ->leftJoin(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME, 'b', 'WITH', 'q = b.question AND b.softDeleted = 0 ')
            ->where('q.softDeleted = 0')
            ->orderBy('q.createdAt', 'DESC')
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
            $qb->innerJoin(ConsultConstants::QUESTION_TAG_ENTITY_NAME, 't', 'WITH', 'q = t.question');
            $qb->andWhere('t.tag IN(:category)');
            $qb->setParameter('category', $category);
        }

        if (isset($practoAccountId)) {
            if (isset($bookmark) and Utility::toBool($bookmark)) {
                $qb->innerJoin(ConsultConstants::USER_ENTITY_NAME, 'u', 'WITH', 'u = q.userInfo');
                $qb->andWhere('u.practoAccountId = :practoAccountID');
                $qb->andWhere('u.softDeleted = 0 ');
                $qb->setParameter('practoAccountID', $practoAccountId);
            } elseif (isset($bookmark) and $bookmark == "true") {
                //$qb->innerJoin(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME, 'b', 'WITH', 'q = b.question AND b.softDeleted = 0 ');
                $qb->andWhere('b.practoAccountId = :practoAccountId');
                $qb->setParameter('practoAccountId', $practoAccountId);
            } else {
                $qb->leftJoin(ConsultConstants::USER_ENTITY_NAME, 'u', 'WITH', ' u=q.userInfo AND u.softDeleted = 0 ');
                //$qb->leftJoin(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME, 'b', 'WITH', 'q = b.question AND b.softDeleted = 0 ');
                $qb->andWhere(' u.practoAccountId = :practoAccountId OR b.practoAccountId = :practoAccountId');

                $qb->setParameter('practoAccountId', $practoAccountId);
            }
        }

        $questionList = $qb->getQuery()->getArrayResult();

        $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
        $count = count($paginator);

        if (is_null($questionList)) {
            return null;
        }

        return array("questions" => $questionList, "count" => $count);
    }


    /**
     * @param string   $thisMonth
     * @param string   $lastMonth
     * @param string   $state
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string   $thisYear
     * @param integer  $limit
     * @param integer  $patientId
     * @param string   $patientName
     * @param integer  $questionID
     *
     * @return array
     *
     */
    public function findModerationQuestionsByFilters($thisMonth, $lastMonth, $state, $startDate, $endDate, $thisYear, $limit, $patientId, $patientName, $questionID)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('q as question', 'count(b.id) AS bookmarkCount')
            ->from(ConsultConstants::QUESTION_ENTITY_NAME, 'q')
            ->leftJoin(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME, 'b', 'WITH', 'q = b.question AND b.softDeleted = 0 ')
            ->where('q.softDeleted = 0')
            ->orderBy('q.modifiedAt', 'DESC')
            ->groupBy('q');

        if (isset($questionID)) {
            $qb->andWhere('q.id = :questionID');
            $qb->setParameter('questionID', $questionID);
        }

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        if ($thisMonth) {
            $qb->andWhere('month(q.createdAt)= :month');
            $datetime = new \DateTime("now");
            $month = $datetime->format('m');
            $qb->setParameter('month', $month);
        }

        if ($lastMonth) {
            $qb->andWhere('month(q.createdAt)= :month');
            $datetime = new \DateTime("last month");
            $month = $datetime->format('m');
            $qb->setParameter('month', $month);
        }

        if ($thisYear) {
            $qb->andWhere('year(q.createdAt)= :year');
            $datetime = new \DateTime("now");
            $year = $datetime->format('Y');
            $qb->setParameter('year', $year);
        }

        if (isset($startDate) && isset($endDate)) {
            $start= substr($startDate, 0, 10);
            $end=substr($endDate, 0, 10);
            $qb->andWhere(':startDate <= q.createdAt');
            $qb->andWhere('q.createdAt <= :endDate');
            $qb->setParameter('startDate', new \DateTime($start));
            $qb->setParameter('endDate', new \DateTime($end));
        }

        if (isset($state)) {
            $qb->andWhere('q.state IN(:state)');
            $qb->setParameter('state', $state);
        }

        if (isset($category)) {
            $qb->innerJoin(ConsultConstants::QUESTION_TAG_ENTITY_NAME, 't', 'WITH', 'q = t.question');
            $qb->andWhere('t.tag IN(:category)');
            $qb->setParameter('category', $category);
        }

        if (isset($patientId)) {
                $qb->leftJoin(ConsultConstants::USER_ENTITY_NAME, 'u', 'WITH', ' u=q.userInfo AND u.softDeleted = 0 ');
                //$qb->leftJoin(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME, 'b', 'WITH', 'q = b.question AND b.softDeleted = 0 ');
                $qb->andWhere(' u.practoAccountId = :practoAccountId');

                $qb->setParameter('practoAccountId', $patientId);

        }

        if (isset($patientName)) {
            if (!(isset($patientId))) {
                $qb->leftJoin(ConsultConstants::USER_ENTITY_NAME, 'u', 'WITH', ' u=q.userInfo AND u.softDeleted = 0 ');
            }
            $qb->andWhere(' u.name = :patientName');
            $qb->setParameter('patientName', $patientName);
        }


        $questionList = $qb->getQuery()->getArrayResult();


        $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
        $totalCount = count($paginator);


        $qbInvalid = $this->_em->createQueryBuilder();
        $qbInvalid = $qb;
        $qbInvalid->andWhere("q.state = 'REJECTED' or q.state='DOCNOTFOUND'");
        $invalidCount = count(new Paginator($qbInvalid->getQuery(), $fetchJoinCollection = true));

        $count = array("totalCount" => $totalCount, "invalidCount" => $invalidCount);

        if (is_null($questionList)) {
            return null;
        }

        return array("questions" => $questionList, "count" => $count);
    }



    /**
     * @return int
     */
    public function totalCount()
    {
        $qb =$this->_em->createQueryBuilder();
        $qb->select('count(q.id)');
        $qb->from(ConsultConstants::QUESTION_ENTITY_NAME, 'q');
        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    /**
     * @return int
     */
    public function thisMonthCount()
    {
        $qb =$this->_em->createQueryBuilder();
        $qb->select('count(q.id)');
        $qb->from(ConsultConstants::QUESTION_ENTITY_NAME, 'q');
        $qb->where('month(q.createdAt)= :month');
        $datetime = new \DateTime("now");
        $month = $datetime->format('m');
        $qb->setParameter('month', $month);
        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    /**
     * @return int
     */
    public function lastMonthCount()
    {
        $qb =$this->_em->createQueryBuilder();
        $qb->select('count(q.id)');
        $qb->from(ConsultConstants::QUESTION_ENTITY_NAME, 'q');
        $qb->where('month(q.createdAt)= :month');
        $datetime = new \DateTime("last month");
        $month = $datetime->format('m');
        $qb->setParameter('month', $month);
        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }


    /**
     * @param \DateTime $startDate - time for the filter
     * @param \DateTime $endDate   - time for the filter
     * @return int
     */
    public function customCount($startDate, $endDate)
    {
        $qb =$this->_em->createQueryBuilder();
        $qb->select('count(q.id)');
        $qb->from(ConsultConstants::QUESTION_ENTITY_NAME, 'q');
        $qb->where(':startDate =< q.createdAt =< :endDate');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }


    /**
     * @param array   $search
     * @param integer $limit
     * @param integer $offset
     *
     * @return array (QuestionList, count)
     */
    public function findSearchQuestions($search, $limit, $offset)
    {
        if (empty($search)) {
            return null;
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('q as question', 'count(b.id) AS bookmarkCount', 'count(q.id) AS matchScore')
            ->from(ConsultConstants::QUESTION_ENTITY_NAME, 'q')
            ->leftJoin(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME, 'b', 'WITH', 'q = b.question AND b.softDeleted = 0 ')
            ->addOrderBy('matchScore', 'DESC')
            ->addOrderBy('q.modifiedAt', 'DESC')
            ->groupBy('q.id')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $qb->innerJoin(ConsultConstants::QUESTION_TAG_ENTITY_NAME, 't', 'WITH', 'q = t.question AND t.softDeleted = 0');
        foreach ($search as $i => $word) {
            $qb->orWhere("LOWER(t.tag) LIKE :word$i");
            $qb->setParameter("word$i", '%'.$word.'%');
        }

        $qb->andWhere('q.state = :state AND q.softDeleted = 0');
        $qb->setParameter('state', 'ANSWERED');
        $questionList = $qb->getQuery()->getArrayResult();

        $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
        $count = count($paginator);

        if (is_null($questionList)) {
            return null;
        }

        return array("questions" => $questionList, "count" => $count);
    }

    /**
     * @param Question $question
     *
     * @return array
     */
    public function getBookmarkCountForAQuestion($question)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(qb.id)')
            ->from(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAME, 'qb')
            ->where('qb.question = :question')
            ->andWhere('qb.softDeleted = 0')
            ->setParameter('question', $question);

        $result = $qb->getQuery()->getArrayResult();

        if (count($result) == 0) {
            return 0;
        }

        return $result[0][1];

    }

    /**
     * @param \ConsultBundle\Entity\Question $question
     *
     * @return array|null
     */
    public function getImagesForAQuestion(Question $question)
    {
        if (empty($question)) {
            return null;
        }
        $qb = $this->_em->createQueryBuilder();
        $qb->select('question_image.url')
            ->from(ConsultConstants::QUESTION_IMAGE_ENTITY_NAME, 'question_image')
            ->where('question_image.question = :question')
            ->andWhere('question_image.softDeleted = 0')
            ->setParameter('question', $question);

        //$qb->getQuery()->setHint('url');
        $images = $qb->getQuery()->getResult();

        if (count($images) == 0) {
            return null;
        }

        return $images;
    }

    /**
     * @param int $questionId
     */
    public function getQuestion($questionId)
    {
        if (!empty($questionId)) {
            $conn = $this->getConnection();


        }
    }

    /**
     * @return mixed
     */
    private function getConnection()
    {
        return  $this->get('database_connection');
    }
}
