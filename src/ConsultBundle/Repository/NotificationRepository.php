<?php

namespace ConsultBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use ConsultBundle\Constants\ConsultConstants;
use Doctrine\ORM\EntityRepository;
use ConsultBundle\Utility\Utility;

/**
 * Notification Repository
 */
class NotificationRepository extends EntityRepository
{
    /**
     * @param integer $practoAccountId - Practo Account Id of Doctor
     * @param bool    $viewed          - Notification already viewed or not
     * @param integer $limit           - limit of query
     * @param integer $offset          - offset of query
     * @param integer $sortBy          - Sort By query
     *
     * @return Array
     */
    public function findDoctorNotificationByFilters($practoAccountId, $viewed, $limit, $offset, $sortBy)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(array('dn'))
            ->from(ConsultConstants::DOCTOR_NOTIFICATION_ENTITY_NAME, 'dn')
            ->where('dn.practoAccountId = :practoAccountId')
            ->setParameter('practoAccountId', $practoAccountId)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if (isset($viewed)) {
            $qb->andWhere('dn.viewed = :viewed');
            if (Utility::toBool($viewed)) {
                $viewed = 1;
            } else {
                $viewed = 0;
            }
            $qb->setParameter('viewed', $viewed);
        }

        if (isset($sortBy)) {
            if ($sortBy == "modified_at") {
                $qb->orderBy('dn.modifiedAt', 'DESC');
            }

            if ($sortBy == "created_at") {
                $qb->orderBy('dn.createdAt', 'DESC');
            }
        }

        $notificationList = $qb->getQuery()->getResult();

        $paginator = new Paginator($qb->getQuery());
        $count = count($paginator);

        if (is_null($notificationList)) {
            return null;
        }

        return array($notificationList, $count);

    }

    /**
     * @param integer $practoAccountId - Practo Account Id of User
     * @param bool    $viewed          - Notification already viewed or not
     * @param integer $limit           - limit of query
     * @param integer $offset          - offset of query
     * @param integer $sortBy          - Sort By query
     *
     * @return Array
     */
    public function findUserNotificationByFilters($practoAccountId, $viewed, $limit, $offset, $sortBy)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(array('un'))
            ->from(ConsultConstants::USER_NOTIFICATION_ENTITY_NAME, 'un')
            ->where('un.practoAccountId = :practoAccountId')
            ->setParameter('practoAccountId', $practoAccountId)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if (isset($viewed)) {
            $qb->andWhere('un.viewed = :viewed');
            if (Utility::toBool($viewed)) {
                $viewed = 1;
            } else {
                $viewed = 0;
            }
            $qb->setParameter('viewed', $viewed);
        }

        if (isset($sortBy)) {
            if ($sortBy == "modified_at") {
                $qb->orderBy('un.modifiedAt', 'DESC');
            }

            if ($sortBy == "created_at") {
                $qb->orderBy('un.createdAt', 'DESC');
            }
        }

        $notificationList = $qb->getQuery()->getResult();

        $paginator = new Paginator($qb->getQuery());
        $count = count($paginator);

        if (is_null($notificationList)) {
            return null;
        }

        return array($notificationList, $count);

    }
}
