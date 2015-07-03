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
     * @param $practoAccountId
     *
     * @return bool
     */
    public function privateThreadExists($practoAccountId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME, 'p')
            ->innerJoin(ConsultConstants::USER_ENTITY_NAME, 'u', 'WITH', 'u = p.userInfo AND u.softDeleted = 0')
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
    public function getPatientPrivateThreads($practoAccountId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d.name as doctor_name', 'd.profilePicture as profile_picture', 'p.subject', 'p.modifiedAt as last_modified_time', '(:FOLLOW_UP_THRESHOLD - COUNT(c)) as follow_up_count')
            ->from(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME, 'p')
            ->innerJoin(ConsultConstants::USER_ENTITY_NAME, 'u', 'WITH', 'u = p.userInfo AND u.softDeleted = 0')
            ->leftJoin(ConsultConstants::CONVERSATION_ENTITY_NAME, 'c', 'WITH', 'c.privateThread = p and c.isDocReply = false and c.softDeleted = 0')
            ->leftJoin(ConsultConstants::DOCTOR_SETTING_ENTITY_NAME, 'd', 'WITH', 'p.doctorId = d.practoAccountId and d.softDeleted = 0')
            ->where('u.practoAccountId = :practoAccountId and p.softDeleted = 0')
            ->setParameter('practoAccountId', $practoAccountId)
            ->setParameter('FOLLOW_UP_THRESHOLD', self::FOLLOW_UP_THRESHOLD);

        $privateThreadEntry = $qb->getQuery()->getArrayResult();

        if (empty($privateThreadEntry)) {
            return null;
        }

        return $privateThreadEntry;
    }

    /**
     * @param int $practoAccountId
     *
     * @return array|null
     */
    public function getDoctorPrivateThreads($practoAccountId)
    {
        $subqb = $this->_em->createQueryBuilder();
        $subqb->select('c.text as question')
              ->from(ConsultConstants::CONVERSATION_ENTITY_NAME, 'c')
              ->where('c.isDocReply = 0')
              ->orderBy('c.createdAt', 'DESC')
              ->setMaxResults(1);
        $lastQuestion = $subqb->getQuery()->getArrayResult();

        $qb = $this->_em->createQueryBuilder();
        $qb->select('p.subject', 'p.modifiedAt as last_modified_time', '(:lastQuestion) as question', 'u as user_info')
            ->from(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME, 'p')
            ->innerJoin(ConsultConstants::USER_ENTITY_NAME, 'u', 'WITH', 'u = p.userInfo AND u.softDeleted = 0')
            ->where('p.doctorId = :practoAccountId and p.softDeleted = 0')
            ->setParameter('practoAccountId', $practoAccountId)
            ->setParameter('lastQuestion', $lastQuestion);

        $privateThreadEntry = $qb->getQuery()->getResult();

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
        $qb->select('COUNT(c)')
            ->from(ConsultConstants::CONVERSATION_ENTITY_NAME, 'c')
            ->leftJoin(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME, 'p', 'WITH', 'p = c.privateThread')
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
        $qb->select('c.id', 'c.text', 'c.isDocReply', 'GROUP_CONCAT(ci.url separator \', \') as images')
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
