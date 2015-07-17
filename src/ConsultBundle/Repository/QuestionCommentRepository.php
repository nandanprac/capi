<?php

namespace ConsultBundle\Repository;

use ConsultBundle\Entity\Question;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use ConsultBundle\Constants\ConsultConstants;
use Doctrine\ORM\EntityRepository;

/**
 * Question Comment Repository
 */

class QuestionCommentRepository extends EntityRepository
{
    /**
     * @param Question $question        - Question object
     * @param integer  $limit           - limit
     * @param integer  $offset          - offset
     * @param integer  $practoAccountId - practo account id
     * @return array (comments, count)
     */
    public function getComments($question, $limit, $offset, $practoAccountId)
    {

        // query for getting comment details with total votes
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            'c.id',
            'c.practoAccountId as practo_account_id',
            'c.identifier as identifier',
            'c.text as text',
            'c.createdAt as created_at',
            'COALESCE(SUM(cv.vote), 0) as total_votes'
        );

        $qb->from(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME, 'c')
            ->leftJoin(ConsultConstants::QUESTION_COMMENT_VOTE_ENTITY_NAME, 'cv', 'WITH', 'c = cv.questionComment and cv.softDeleted = 0')
            ->where('c.softDeleted = 0')
            ->andWhere('c.question = :question')
            ->setParameter('question', $question)
            ->groupBy('c.id')
            ->orderBy('c.createdAt', 'ASC');

        if (!empty($limit)) {
            $qb->setMaxResults($limit);
        }
        if (!empty($offset)) {
            $qb->setFirstResult($offset);
        }

        if (!empty($practoAccountId)) {
            $qb->addSelect('COALESCE(cv1.vote, 0) as has_voted')
               ->leftJoin(ConsultConstants::QUESTION_COMMENT_VOTE_ENTITY_NAME, 'cv1', 'WITH', 'c = cv1.questionComment and cv1.practoAccountId = :practoAccountId and cv1.softDeleted = 0');

            $qb->addSelect('cf.flagCode as flag', 'cf.flagText as flag_text')
                ->leftJoin(ConsultConstants::QUESTION_COMMENT_FLAG_ENTITY_NAME, 'cf', 'WITH', 'c = cf.questionComment and cf.practoAccountId = :practoAccountId and cf.softDeleted = 0');

            $qb->setParameter('practoAccountId', $practoAccountId);
        }

        $commentList = $qb->getQuery()->getArrayResult();


        $countQuery = $qb->getQuery();
        $countQuery->setFirstResult(null)->setMaxResults(null);
        $count =  count($countQuery->getArrayResult());

        if (empty($commentList)) {
            return null;
        }

        return array('comments' => $commentList, 'count' => $count);
    }


    ///////////////////////////

    /**
     * @param Question $question        - Question object
     * @return array (comments, count)
     */
    public function getModerationComments($question)
    {

        // query for getting comment details with total votes
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            'c.id',
            'c.practoAccountId as practo_account_id',
            'c.identifier as identifier',
            'c.text as text',
            'c.createdAt as created_at',
            'COALESCE(SUM(cv.vote), 0) as total_votes'
        );

        $qb->from(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME, 'c')
            ->leftJoin(ConsultConstants::QUESTION_COMMENT_VOTE_ENTITY_NAME, 'cv', 'WITH', 'c = cv.questionComment and cv.softDeleted = 0')
            ->where('c.softDeleted = 0')
            ->andWhere('c.question = :question')
            ->setParameter('question', $question)
            ->groupBy('c.id')
            ->orderBy('c.createdAt', 'DESC');

        $qb->addSelect('cf.flagCode as flag', 'cf.flagText as flag_text','cf.createdAt as flag_create','cf.id as flagID')
            ->leftJoin(ConsultConstants::QUESTION_COMMENT_FLAG_ENTITY_NAME, 'cf', 'WITH', 'c = cf.questionComment and cf.softDeleted = 0');

        $commentList = $qb->getQuery()->getArrayResult();

        $count =  count($commentList);


        if (empty($commentList)) {
            return null;
        }

        return array('questionID'=>$question->getId(),'comments' => $commentList, 'count' => $count);
    }




    ///////////////////////////
    /**
     * @param int $questionCommentId
     * @param int $practoAccountId
     *
     * @return null
     */
    public function loadComment($questionCommentId, $practoAccountId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            'c.id',
            'c.practoAccountId as practo_account_id',
            'c.identifier as identifier',
            'c.text as text',
            'c.createdAt as created_at',
            'COALESCE(SUM(cv.vote), 0) as total_votes'
        );

        $qb->from(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME, 'c')
            ->leftJoin(ConsultConstants::QUESTION_COMMENT_VOTE_ENTITY_NAME, 'cv', 'WITH', 'c = cv.questionComment and cv.softDeleted = 0')
            ->where('c.softDeleted = 0')
            ->andWhere('c.id = :questionCommentId')
            ->setParameter('questionCommentId', $questionCommentId)
            ->groupBy('c.id');

        if (!empty($practoAccountId)) {
            $qb->addSelect('COALESCE(cv1.vote, 0) as has_voted')
               ->leftJoin(ConsultConstants::QUESTION_COMMENT_VOTE_ENTITY_NAME, 'cv1', 'WITH', 'c = cv1.questionComment and cv1.practoAccountId = :practoAccountId and cv1.softDeleted = 0');

            $qb->addSelect('cf.flagCode as flag', 'cf.flagText as flag_text')
                ->leftJoin(ConsultConstants::QUESTION_COMMENT_FLAG_ENTITY_NAME, 'cf', 'WITH', 'c = cf.questionComment and cf.practoAccountId = :practoAccountId and cf.softDeleted = 0');

            $qb->setParameter('practoAccountId', $practoAccountId);
        }

        $comment = $qb->getQuery()->getArrayResult();

        if (empty($comment)) {
            return null;
        }

        return $comment[0];
    }

}
