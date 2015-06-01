<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use ConsultBundle\Entity\Question;
use ConsultBundle\Manager\ValidationError;
use ConsultBundle\Entity\User;

/**
 * User profile details Manager
 */
class UserProfileManager extends BaseManager
{
    public function updateFields($userInfo, $requestParams) {
        $userInfo->setAttributes($requestParams);

        try {
            $this->validator->validate($userInfo);
        } catch(ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }
        return;
    }

    public function add($requestParams) {
        $user = new User();
        $this->updateFields($user, $requestParams);
        $this->helper->persist($user, 'true');
        return $user;
    }

}

