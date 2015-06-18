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
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     */
    protected function authenticate($throwException = true)
    {
        if (Utility::toBool($_SESSION['validated'])) {
            return $_SESSION['authenticated_user']['id'];
        } elseif ($throwException) {
            throw new HttpException(Codes::HTTP_FORBIDDEN, "Unauthorised Access");
        }

        return null;

    }
}
