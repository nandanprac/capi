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
     * @param integer $limit           - limit
     * @param integer $offset          - offset
     * @param integer $practoAccountId - practo account id
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
            'COALESCE(SUM(cv.vote), 0) as total_votes'
        );

        $qb->from(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME, 'c')
            ->leftJoin(ConsultConstants::QUESTION_COMMENT_VOTE_ENTITY_NAME, 'cv', 'WITH', 'c = cv.questionComment and cv.softDeleted = 0')
            ->where('c.softDeleted = 0')
            ->andWhere('c.question = :question')
            ->setParameter('question', $question)
            ->groupBy('c.id')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $commentList = $qb->getQuery()->getArrayResult();

        // query for getting vote details for a particulat practo_account_id
        if (!empty($practoAccountId)) {
            $qb1 = $this->_em->createQueryBuilder();
            $qb1->select('c.id', 'CASE
                            WHEN cv.practoAccountId = :practoAccountId then cv.vote
                            ELSE 0
                                END as has_voted')
                ->from(ConsultConstants::QUESTION_COMMENT_VOTE_ENTITY_NAME, 'cv')
                ->where('cv.practoAccountId = :practoAccountId')
                ->leftJoin(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME, 'c', 'WITH', 'c = cv.questionComment')
            ->setParameter('practoAccountId', $practoAccountId);

            $voteList = $qb1->getQuery()->getArrayResult();

            $refinedComments = array();
            foreach ($commentList as $comment) {
                $comment = $this->mergeVote($voteList, $comment);
                array_push($refinedComments, $comment);
            }
        }


        //paginator cannot be used to retrieve count of queries where more than one table is involved
        $countQuery = $qb->getQuery();
        $countQuery->setFirstResult(null)->setMaxResults(null);
        $count =  count($countQuery->getArrayResult());

        if (!empty($refinedComments)) {
            $comments = $refinedComments;
        } else if (!empty($commentList)) {
            $comments = $commentList;
        } else {
            return null;
        }

        return array('comments' => $comments, 'count' => $count);
    }

    private function mergeVote($voteList, $comment)
    {
        foreach ($voteList as $vote) {
            if ($vote['id'] == $comment['id']) {
                $comment['has_voted'] = $vote['has_voted'];

                return $comment;
            }
        }
        $comment['has_voted'] = 0;

        return $comment;
    }
}
