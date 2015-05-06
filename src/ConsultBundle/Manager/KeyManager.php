<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Entity\Key;
use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Helper\Helper;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Temp Manager
 */
class KeyManager extends BaseManager
{
    /**
     * Update Fields
     *
     * @param UserInfo      $userEntry
     * @param array         $requestParams     - Request parameters
     *
     * @return null
     */
    public function updateFields($key, $requestParams)
    {
        $errors = array();
        $key->setKey($requestParams['key']);

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
        $key = new Key();

        $this->updateFields($key, $requestParams);
        $this->helper->persist($key, true);

        return $key;
    }

    /**
     * Load User's Additional Info By Id
     *
     * @return userEntry
     */
    public function loadAll()
    {
        $temp = $this->helper->getRepository(ConsultConstants::$TEMP_ENTITY_NAME)->loadAll();

        if (is_null($temp)) {
            return null;
        }

        return $temp;
    }
}
