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
     * @param UpdateAccountsUtil $updateAccountsUtil
     */
    public function __construct(UpdateAccountsUtil $updateAccountsUtil)
    {
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
     * @param array  $requestParams
     * @param string $profileToken
     *
     * @return \ConsultBundle\Entity\UserInfo|mixed
     * @throws \ConsultBundle\Manager\ValidationError
     */
    public function add($requestParams, $profileToken)
    {
        $error = array();
        if (!array_key_exists('practo_account_id', $requestParams)) {
            @$error['practo_account_id'] = 'This value cannot be blank';
            throw new ValidationError($error);
        }

        if (array_key_exists('id', $requestParams) and !empty($requestParams['id'])) {
            $userEntry = $this->helper->loadById($requestParams['id'], ConsultConstants::USER_ENTITY_NAME);

            if (empty($userEntry)) {
                @$error['error'] = 'Invalid user_info id';
                throw new ValidationError($error);
            }
            if ($userEntry->getPractoAccountId() != $requestParams['practo_account_id']) {
                @$error['error'] = 'This user_info id does not belong to this practo_account_id';
                throw new ValidationError($error);
            }

            if (!$userEntry->isIsRelative()) {
                $this->updateAccountsUtil->updateAccountDetails($profileToken, $requestParams);
            }

        } else {
            $userEntry = new UserInfo();
            if (array_key_exists('is_relative', $requestParams) and Utility::toBool($requestParams['is_relative'])) {
                if (!array_key_exists('name', $requestParams)) {
                    @$error['name'] = 'This value cannot be blank when a new profile is being created';
                }
                if (!array_key_exists('gender', $requestParams)) {
                    @$error['gender'] = 'This value cannot be blank when a new profile is being created';
                }
                if (!array_key_exists('age', $requestParams)) {
                    @$error['age'] = 'This value cannot be blank when a new profile is being created';
                }
                if (count($error) > 0) {
                    throw new ValidationError($error);
                }

            } else {
                $er = $this->helper->getRepository(ConsultConstants::USER_ENTITY_NAME);
                $entry = $er->findOneBy(array('practoAccountId' => $requestParams['practo_account_id'], 'isRelative' => 0));
                if (!empty($entry)) {
                    $userEntry = $entry;
                }
                $this->updateAccountsUtil->updateAccountDetails($profileToken, $requestParams);
            }
        }

        $requestParams['gender'] = (array_key_exists('gender', $requestParams)) ? strtoupper($requestParams['gender']) : null;
        $this->updateFields($userEntry, $requestParams);
        $this->helper->persist($userEntry, true);

        return $userEntry;
    }

    /**
     * Load User's Additional Info By Id
     *
     * @param array $requestParams
     * @throws ValidationError
     * @return array userEntry
     */
    public function load($requestParams)
    {
        if (!array_key_exists('practo_account_id', $requestParams)) {
            @$error['practo_account_id'] = 'This value cannot be blank';
            throw new ValidationError($error);
        }

        $userEntries = $this->helper->getRepository(ConsultConstants::USER_ENTITY_NAME)->findBy(
            array('practoAccountId' => $requestParams['practo_account_id']),
            array('createdAt' => 'ASC')
        );

        if (empty($userEntries)) {
            return null;
        }

        return array('user_profiles' => $userEntries);
    }
}
