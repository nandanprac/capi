<?php

namespace ConsultBundle\Repository;

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
     * @param integer $questionId - Question id
     * @param integer $limit      - limit
     * @param integer $offset     - offset
     * @return array (comments, count)
     */
    public function getComments($question, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c.practoAccountId as practo_account_id', 
                    'c.phoneNumber as number_to_identify',
                    'c.text as text',
                    'COALESCE(SUM(cv.vote), 0) as votes')
            ->from(ConsultConstants::QUESTION_COMMENT_ENTITY_NAME, 'c')
            ->leftJoin(ConsultConstants::QUESTION_COMMENT_VOTE_ENTITY_NAME, 'cv', 'WITH', 'c = cv.questionComment and cv.softDeleted = 0')
            ->where('c.softDeleted = 0')
            ->andWhere('c.question = :question')
            ->setParameter('question', $question)
            ->groupBy('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $commentList = $qb->getQuery()->getArrayResult();

        //paginator cannot be used to retrieve count of queries where more than one table is involved
        $countQuery = $qb->getQuery();
        $countQuery->setFirstResult(null)->setMaxResults(null);
        $count =  count($countQuery->getArrayResult());

        if (is_null($commentList)) {
            return null;
        }

        return array('comments' => $commentList, 'count' => $count);
    }
}
