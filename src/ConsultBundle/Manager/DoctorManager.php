<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 18:26
 */

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Repository\DoctorRepository;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Doctor manager
 */
class DoctorManager extends BaseManager
{

    private static $mandatoryFieldsForPostDoctorConsult = array(
        "practo_account_id",
        "doctor_fabric_id",
        "name",
        "speciality",
        "location",
        "timezone",
        );
    /**
     * @param array $queryParams
     *
     * @return array|null
     * @throws \Exception
     */
    public function loadAllForDoctor($queryParams)
    {
        $doctorId = array_key_exists('practo_account_id', $queryParams) ? $queryParams['practo_account_id'] : null;

        if (null == $doctorId) {
            throw new \Exception(array("error"=>"Please pass practo_account_id"));
        }

        try {
            $detailList = $this->getRepository()->findByFilters($doctorId, $queryParams);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if (null == $detailList) {
            return null;
        }

        return array("details"=>$detailList);
    }

    /**
     * sample request:
     *
     * {
     * "practo_account_id": 1,
     * "doctor_fabric_id": 1,
     * "name": "Rachit Mishra",
     * "profile_picture": "http://google.com",
     * "speciality": "Dentist",
     * "location": "Bangalore",
     * "timezone": "Asia/Mumbai",
     * "num_ques_day": "3",
     * "preferred_consultation_timings": 0,
     * "consultation_days": "1111100"
     * }
     *
     * @param array   $postData
     * @param boolean $post
     * @return \ConsultBundle\Entity\DoctorConsultSettings
     */
    public function putConsultSettings(array $postData, $post = false)
    {
        if (empty($postData)) {
            throw new HttpException("Data is empty", Codes::HTTP_BAD_REQUEST);
        }

        if (array_key_exists('doctor_fabric_id', $postData)) {
            $postData['fabric_doctor_id'] = $postData['doctor_fabric_id'];
        }

        //check for mandatory fields
        $this->helper->checkForMandatoryFields(self::$mandatoryFieldsForPostDoctorConsult, $postData);

        /**
         * @var DoctorRepository $er
         */
        $er = $this->getRepository();


        /**
         * @var DoctorConsultSettings $doctor
         */
        $doctor  = $er->findOneBy(
            array(
                "fabricDoctorId" => $postData['doctor_fabric_id'],
                "softDeleted" => 0,
            )
        );

        if (!$post) {
            if (empty($doctor)) {
                throw new HttpException(Codes::HTTP_BAD_REQUEST, "Instance doesn't exist");
            }

            $practoAccountId = $postData['practo_account_id'];

            if ($doctor->getPractoAccountId() != $practoAccountId) {
                throw new HttpException("Account Id is not same", Codes::HTTP_BAD_REQUEST);
            }
        } elseif (!empty ($doctor)) {
            throw new HttpException(Codes::HTTP_BAD_REQUEST, "Doctor already exist in system: Duplicate doctor_fabric_id");
        } else {
            $doctor  = $er->findOneBy(
                array(
                    "practoAccountId" => $postData['practo_account_id'],
                    "softDeleted" => 0,
                )
            );

            if (!empty($doctor)) {
                throw new HttpException(Codes::HTTP_BAD_REQUEST, "Doctor already exist in system: Duplicate practo_account_id");
            }

            $doctor = new DoctorConsultSettings();
        }



        if (array_key_exists('consultation_days', $postData) && !empty($postData['consultation_days'])) {
            $consultationDays = bindec($postData['consultation_days']);
            $postData['consultation_days'] = $consultationDays;
        }



        $this->updateFields($doctor, $postData);


        $this->helper->persist($doctor, true);

        $consultationDays = $doctor->getConsultationDays();

        if (!empty($consultationDays)) {
            $consultationDaysBin = decbin($consultationDays);
            $len = strlen($consultationDaysBin);
            $addedZeroes = "";
            for (; $len < 7; $len++) {
                $addedZeroes = "0".$addedZeroes;
            }
            $consultationDaysBin = $addedZeroes.$consultationDaysBin;
            $doctor->setConsultationDaysStr($consultationDaysBin);
            $doctor->setConsultationDays(null);
        }

        return $doctor;
    }

    /**
     * @param int $id
     *
     * @return null
     */
    public function getConsultSettings($id)
    {
        if (empty($id)) {
            return null;
        }

        /**
         * @var DoctorRepository $er
         */
        $er = $this->getRepository();

        /**
         * @var DoctorConsultSettings $result
         */
        $result =  $result = $er->findOneBy(
            array(
                "fabricDoctorId" => $id,
                "softDeleted" => 0,
                )
        );

        if (empty($result)) {
            return null;
        }



        $authenticatedPractoAccountId = $_SESSION['authenticated_user']['id'];

        $practoAccountId = $result->getPractoAccountId();

        if ($practoAccountId != $authenticatedPractoAccountId) {
            throw new HttpException(Codes::HTTP_FORBIDDEN, "Unauthorised access");
        }

        $consultationDays = $result->getConsultationDays();
        if (!empty($consultationDays)) {
            $consultationDaysBin = decbin($consultationDays);
            $len = strlen($consultationDaysBin);
            $addedZeroes = "";
            for (; $len < 7; $len++) {
                $addedZeroes = "0".$addedZeroes;
            }
            $consultationDaysBin = $addedZeroes.$consultationDaysBin;
            $result->setConsultationDaysStr($consultationDaysBin);
            $result->setConsultationDays(null);

        }

        return $result;
    }

    /**
     * @param int $id
     *
     * @return null
     */
    public function getConsultSettingsByPractoAccountId($id)
    {
        if (empty($id)) {
            return null;
        }

        /**
         * @var DoctorRepository $er
         */
        $er = $this->getRepository();

        $result =  $result = $er->findOneBy(
            array(
                "practoAccountId" => $id,
                "softDeleted" => 0,
                )
        );

        /* if (empty($result)) {
             $result = $this->helper->loadById(1, ConsultConstants::DOCTOR_SETTING_ENTITY_NAME);
         }*/

        return $result;
    }

    /**
<<<<<<< HEAD
     * @param array $postData
     *
||||||| merged common ancestors
     * @param $postData
     *
=======
     * @param array   $postData
     * @param boolean $dev
>>>>>>> user_consent
     * @return \ConsultBundle\Entity\DoctorConsultSettings
     * @throws \ConsultBundle\Manager\ValidationError
     */
    public function postConsultSettings($postData, $dev=false)
    {
        if ($dev) {
            $doc = new DoctorConsultSettings();
            $this->updateFields($doc, $postData);
            $this->helper->persist($doc, true);

            return $doc;
        } else {
            $this->putConsultSettings($postData);
        }

    }

    /**
     * @param string $city
     * @param string $speciality
     *
     * @return null
     * @throws \Exception
     */
    public function getAppropriateDoctors($city, $speciality)
    {
        try {
            $doctors = $this->getRepository()->findBySpecialityAndCity($city, $speciality);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if (empty($doctors)) {
            return null;
        }

        return $doctors;
    }


    /**
     * @return \Doctrine\ORM\EntityRepository|null
     */
    private function getRepository()
    {

        return $this->helper->getRepository(ConsultConstants::DOCTOR_SETTING_ENTITY_NAME);
    }

    /**
     * @param \ConsultBundle\Entity\DoctorConsultSettings $doctor
     * @param                                             $requestParams
     *
     * @throws \ConsultBundle\Manager\ValidationError
     */
    private function updateFields(DoctorConsultSettings $doctor, $requestParams)
    {
        $doctor->setAttributes($requestParams);


        try {
            $this->validator->validate($doctor);
        } catch (ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return;
    }
}
