<?php

namespace ConsultBundle\Repository;

use ConsultBundle\Utility\Utility;
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
            ->orderBy('q.modifiedAt', 'DESC')
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
        //var_dump($questionList);die;
        $paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
        $count = count($paginator);

        if (is_null($questionList)) {
            return null;
        }

        return array("questions" => $questionList, "count" => $count);
    }

    /**
     * @param int $questionid
     */
    public function getBookmarkCountForAQuestion($questionid)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(qb.id)')
           ->from(ConsultConstants::QUESTION_BOOKMARK_ENTITY_NAMEENTITY_NAME, 'qb');
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
