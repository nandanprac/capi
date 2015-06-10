<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Utility\Utility;
use ConsultBundle\Entity\UserInfo;
use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Helper\Helper;
use ConsultBundle\Utility\UpdateAccountsUtil;

/**
 * User Info Manager
 */
class UserManager extends BaseManager
{
    protected $updateAccountsUtil;

    /**
     * @param UpdateAccountsUtil        $updateAccountsUtil
     */
    public function __construct(UpdateAccountsUtil $updateAccountsUtil) {
        $this->updateAccountsUtil = $updateAccountsUtil;
    }

    /**
     * Update Fields
     *
     * @param UserInfo $userEntry     - UserInfo object
     * @param array    $requestParams - Request parameters
     *
     * @return null
     */
    public function updateFields($userEntry, $requestParams)
    {
        $userEntry->setAttributes($requestParams);

        try {
            $this->validator->validate($userEntry);
        } catch (ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return ;
    }

    /**
     * Add additional info entry for a user
     *
     * @param array $requestParams
     *
     * @return Added Entry
     */
    public function add($requestParams, $profileToken)
    {
        if (!array_key_exists('practo_account_id', $requestParams)) {
            @$error['practo_account_id'] = 'This value cannot be blank';
            throw new ValidationError($error);
        }

        if (array_key_exists('id', $requestParams) and !empty($requestParams['id'])) {
            $userEntry = $this->helper->loadById($requestParams['id'], ConsultConstants::USER_ENTITY_NAME);

            if(empty($userEntry)) {
                @$error['error'] = 'Invalid user_info id';
                throw new ValidationError($error);
            }
            if($userEntry->getPractoAccountId() != $requestParams['practo_account_id']) {
                @$error['error'] = 'This user_info id does not belong to this practo_account_id';
                throw new ValidationError($error);
            }

            if (!$userEntry->isIsRelative()) {
                $this->updateAccountsUtil->updateAccountDetails($profileToken, $requestParams);
            } 
                        
        } else {
            $userEntry = new UserInfo();
            if (!array_key_exists('is_relative', $requestParams) or 
                (array_key_exists('is_relative', $requestParams) and !(Utility::toBool($requestParams['is_relative'])))) {

                $er = $this->helper->getRepository(ConsultConstants::USER_ENTITY_NAME);
                $entry = $er->findOneBy(array('practoAccountId' => $requestParams['practo_account_id'], 'isRelative' => 0));
                if (!empty($entry)) {
                    $userEntry = $entry;
                }
                $this->updateAccountsUtil->updateAccountDetails($profileToken, $requestParams);
            }
        }

        $this->updateFields($userEntry, $requestParams);
        $this->helper->persist($userEntry, true);

        return $userEntry;
    }

    /**
     * Load User's Additional Info By Id
     *
     * @param integer $userId - User's id
     * @return array userEntry
     */
    public function load($userId)
    {
        $userEntries = $this->helper->getRepository(ConsultConstants::USER_ENTITY_NAME)->findBy(
            array('practoAccountId' => $userId),
            array('createdAt' => 'ASC')
        );

        if (empty($userEntries)) {
            return null;
        }

        return $userEntries;
    }
}
