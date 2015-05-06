<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Entity\Keep;
use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Helper\Helper;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Temp Manager
 */
class KeepManager extends BaseManager
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
        $key->setKeep($requestParams['key']);

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
        $keep = new Keep();

        $this->updateFields($keep, $requestParams);
        $this->helper->persist($keep, true);

        return $keep;
    }

    /**
     * Load User's Additional Info By Id
     *
     * @return userEntry
     */
    public function load()
    {
        $keep = $this->helper->loadAll(ConsultConstants::$KEEP_ENTITY_NAME);

        if (is_null($keep)) {
            return null;
        }

        return $keep;
    }
}
