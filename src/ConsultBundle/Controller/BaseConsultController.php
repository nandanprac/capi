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

    protected function checkPatientConsent($practoAccountId, $throwException = true)
    {
        $userManager = $this->get('consult.user_manager');
        $userConsent = $userManager->checkConsultEnabled($practoAccountId);
        if (!$userConsent && $throwException) {
            throw new HttpException(Codes::HTTP_FORBIDDEN, "User has not consented to use Consult ");
        }

        return $userConsent;
    }


    protected function authenticateForDoctor($throwException = true, $checkForConsent = true)
    {
        $practoAccountId = $this->authenticate($throwException);
        $doctorManager = $this->get('consult.doctor_manager');
        $doctor = $doctorManager->getConsultSettingsByPractoAccountId($practoAccountId);

        if (empty($doctor)) {
            throw new HttpException(Codes::HTTP_FORBIDDEN, "Unauthorised Access");
        }

        if (!$doctor->isConsentGiven() && $checkForConsent) {
            throw new HttpException(Codes::HTTP_FORBIDDEN, "Consent not given for using Consult");
        }

        return $practoAccountId;
    }
}
