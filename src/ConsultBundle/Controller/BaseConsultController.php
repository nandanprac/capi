<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 17/06/15
 * Time: 15:11
 */

namespace ConsultBundle\Controller;

use FOS\RestBundle\Util\Codes;
use ConsultBundle\Utility\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BaseConsultController
 *
 * @package ConsultBundle\Controller
 */
class BaseConsultController extends Controller
{
    /**
     * @param bool $throwException
     *
     * @return null
     * @throws \HttpException
     */
    protected function authenticate($throwException = true)
    {
        if (Utility::toBool($_SESSION['validated'])) {
            return $_SESSION['authenticated_user']['id'];
        } elseif ($throwException) {
            throw new \HttpException('Not authorised to access', Codes::HTTP_FORBIDDEN);
        }

        return null;

    }
}
