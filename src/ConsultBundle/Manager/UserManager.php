<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Entity\UserInfo;
use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Helper\Helper;

/**
 * User Info Manager
 */
class UserManager extends BaseManager
{
    /**
     * Update Fields
     *
     * @param UserInfo      $userEntry
     * @param array         $requestParams     - Request parameters
     *
     * @return null
     */
    public function updateFields($userEntry, $requestParams)
    {
        $userEntry->setAttributes($requestParams);

        return ;
    }

    /**
     * Add additional info entry for a user
     *
     * @param array $requestParams
     *
     * @return Added Entry
     */
    public function add($requestParams)
    {
        $userEntry = new UserInfo();
        $userEntry->setCreatedAt(new \DateTime('now'));
        $userEntry->setSoftDeleted(false);

        $this->updateFields($userEntry, $requestParams);
        $this->helper->persist($userEntry, true);

        return $userEntry;
    }

    /**
     * Load User's Additional Info By Id
     *
     * @return userEntry
     */
    public function load($userId)
    {
        $userEntry = $this->helper->getRepository(ConsultConstants::$USER_ENTITY_NAME)->findOneBy(
                                                                        array('practoAccountId' => $userId),
                                                                        array('createdAt' => 'DESC'));

        if (is_null($userEntry)) {
            return null;
        }

        return $userEntry;
    }
}
