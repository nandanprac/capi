<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 17/06/15
 * Time: 15:11
 */

namespace ConsultBundle\Controller;

use ConsultBundle\Utility\Utility;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
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
            return View::create(json_encode("Unauthorised Access", true), Codes::HTTP_BAD_REQUEST);
        }

        return null;

    }
}
