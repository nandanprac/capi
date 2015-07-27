<?php

namespace ConsultBundle\Repository;

use ConsultBundle\Entity\PrivateThread;
use ConsultBundle\Utility\Utility;
use Doctrine\ORM\Tools\Pagination\Paginator;
use ConsultBundle\Constants\ConsultConstants;
use Doctrine\ORM\EntityRepository;

/**
 * Private Thread Repository
 */
class PrivateThreadRepository extends EntityRepository
{
    const FOLLOW_UP_THRESHOLD = 5;

    /**
     * @param int $practoAccountId
     *
     * @return bool
     */
    public function privateThreadExists($practoAccountId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME, 'p')
            ->innerJoin(ConsultConstants::USER_ENTITY_NAME, 'u', 'WITH', 'u = p.userInfo')
            ->where('u.practoAccountId = :practoAccountId and p.softDeleted = 0')
            ->setParameter('practoAccountId', $practoAccountId);

        $privateThreadEntry = $qb->getQuery()->getArrayResult();

        if (empty($privateThreadEntry)) {
            return false;
        }

        return true;
    }

    /**
     * @param int $practoAccountId
     *
     * @return array|null
     */
    public function getPatientPrivateThreads($practoAccountId, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            'p.id',
            'd.name as doctor_name',
            'd.profilePicture as profile_picture',
            'p.subject',
            'p.modifiedAt as last_modified_time',
            '(:FOLLOW_UP_THRESHOLD - COUNT(c)) as followups_remaining'
        )
            ->from(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME, 'p')
            ->innerJoin(
                ConsultConstants::USER_ENTITY_NAME,
                'u',
                'WITH',
                'u = p.userInfo AND u.softDeleted = 0'
            )
            ->innerJoin(
                ConsultConstants::CONVERSATION_ENTITY_NAME,
                'c',
                'WITH',
                'c.privateThread = p and c.isDocReply = false and c.softDeleted = 0'
            )
            ->innerJoin(
                ConsultConstants::DOCTOR_SETTING_ENTITY_NAME,
                'd',
                'WITH',
                'p.doctorId = d.practoAccountId and d.softDeleted = 0'
            )
            ->where('u.practoAccountId = :practoAccountId and p.softDeleted = 0')
            ->setParameter('practoAccountId', $practoAccountId)
            ->setParameter('FOLLOW_UP_THRESHOLD', self::FOLLOW_UP_THRESHOLD)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $privateThreadEntry = $qb->getQuery()->getArrayResult();

        if (empty($privateThreadEntry)) {
            return null;
        }

        if (count($privateThreadEntry) === 1
            && $privateThreadEntry[0]['id'] === null
        ) {
            return null;
        }

        return $privateThreadEntry;
    }

    /**
     * @param int $practoAccountId
     *
     * @return array|null
     */
    public function getDoctorPrivateThreads($practoAccountId, $limit, $offset)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb2= $this->_em->createQueryBuilder();
        $qb->select('p.id', 'p.subject', 'p.modifiedAt as last_modified_time', 'c1.text as question', 'count(DISTINCT ci.id) as images_count', 'u as user_info')
            ->from(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME, 'p')
            ->innerJoin(ConsultConstants::USER_ENTITY_NAME, 'u', 'WITH', 'u = p.userInfo AND u.softDeleted = 0')
            ->innerJoin(ConsultConstants::CONVERSATION_ENTITY_NAME, 'c1', 'WITH', 'c1.privateThread = p and c1.softDeleted = 0')
            ->leftJoin(ConsultConstants::CONVERSATION_ENTITY_NAME, 'c', 'WITH', 'c.privateThread = p and c.softDeleted = 0')
            ->leftJoin(ConsultConstants::CONVERSATION_IMAGE_ENTITY_NAME, 'ci', 'WITH', 'ci.conversation = c and ci.softDeleted = 0')
            ->add(
                'where',
                $qb->expr()->in(
                    'c1.id',
                    $qb2->select('max(c2.id)')
                        ->from(ConsultConstants::CONVERSATION_ENTITY_NAME, 'c2')
                        ->where('c2.privateThread = p')
                        ->getDQL())
            )
            ->andwhere('p.doctorId = :practoAccountId and p.softDeleted = 0')
            ->groupBy('p.id')
            ->setParameter('practoAccountId', $practoAccountId)
            ->setMaxResults($limit)
            ->setFirstResult($offset);


        $query = $qb->getQuery()->getSQL();
        //var_dump($query);
        $privateThreadEntry = $qb->getQuery()->getResult();
        //var_dump($privateThreadEntry);
        if (empty($privateThreadEntry)) {
            return null;
        }

        return $privateThreadEntry;
    }

    /**
     * @param int           $practoAccountId
     * @param PrivateThread $privateThread
     *
     * @return int
     */
    public function checkFollowUpCount($practoAccountId, $privateThread)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(c.id)')
            ->from(ConsultConstants::CONVERSATION_ENTITY_NAME, 'c')
            ->innerJoin(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME, 'p', 'WITH', 'p = c.privateThread')
            ->innerJoin(ConsultConstants::USER_ENTITY_NAME, 'u', 'WITH', 'u = p.userInfo AND u.softDeleted = 0')
            ->where('p = :privateThread')
            ->andWhere('u.practoAccountId = :practoAccountId')
            ->andWhere('c.isDocReply = false')
            ->setParameter('privateThread', $privateThread)
            ->setParameter('practoAccountId', $practoAccountId);

        return intval(self::FOLLOW_UP_THRESHOLD - $qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param int $privateThread
     *
     * @return array|null
     */
    public function getAllConversationsForThread($privateThread)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('c.id as id', 'c.text as text', 'c.isDocReply as is_doc_reply', 'c.createdAt as created_at', 'GROUP_CONCAT(ci.url separator \', \') as images')

            ->from(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME, 'p')
            ->innerJoin(ConsultConstants::CONVERSATION_ENTITY_NAME, 'c', 'WITH', 'c.privateThread = p AND c.softDeleted = 0')
            ->leftJoin(ConsultConstants::CONVERSATION_IMAGE_ENTITY_NAME, 'ci', 'WITH', 'ci.conversation = c AND ci.softDeleted = 0')
            ->where('p = :privateThread')
            ->groupBy('c.id')
            ->setParameter('privateThread', $privateThread);

        $conversationList = $qb->getQuery()->getArrayResult();

        if (empty($conversationList)) {
            return null;
        }

        return $conversationList;
    }
}
