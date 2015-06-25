<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 18:26
 */

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\DoctorConsultSettings;
use ConsultBundle\Entity\DoctorQuestion;
use ConsultBundle\Repository\DoctorRepository;
use ConsultBundle\Utility\Utility;
use Doctrine\Common\Collections\ArrayCollection;
use ConsultBundle\Manager\ValidationError;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Doctor manager
 */
class DoctorManager extends BaseManager
{

    private static $mandataoryFieldsForPostDoctorConsult = array(
        "practo_account_id",
        "doctor_fabric_id",
        "name",
        "speciality",
        "location",
        "timezone");
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
     * @param array $postData
     *
     * @return \ConsultBundle\Entity\DoctorConsultSettings
     */
    public function putConsultSettings(array $postData)
    {
        if (empty($postData)) {
            throw new HttpException("Data is empty", Codes::HTTP_BAD_REQUEST);
        }

        //check for mandatory fields
        $this->helper->checkForMandatoryFields(self::$mandataoryFieldsForPostDoctorConsult, $postData);

        /**
         * @var DoctorRepository $er
         */
        $er = $this->getRepository();


        /**
         * @var DoctorConsultSettings $doctor
         */
        $doctor =  $result = $er->findOneBy(array(
                "fabricDoctorId" => $postData['doctor_fabric_id'],
                "softDeleted" => 0)
        );

        if (empty($doctor)) {
            throw new HttpException("Instance doesn't exist", Codes::HTTP_BAD_REQUEST);
        }

        $practoAccountId = $postData['practo_account_id'];

        if ($doctor->getPractoAccountId() != $practoAccountId) {
            throw new HttpException("Account Id is not same", Codes::HTTP_BAD_REQUEST);
        }


        if (array_key_exists('consultation_days', $postData) && !empty($postData['consultation_days'])) {
            $consultationDays = bindec($postData['consultation_days']);
            $postData['consultation_days'] = $consultationDays;
        }



        $this->updateFields($doctor, $postData);


        $this->helper->persist($doctor, true);

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

        $result =  $result = $er->findOneBy(array(
                "fabricDoctorId" => $id,
                "softDeleted" => 0)
        );

        if (empty($result)) {
            return null;
        }

        $authenticatedPractoAccountId = $_SESSION['authenticated_user']['id'];
         $practoAccountId = $result->getPractoAccountId();

        if ($result->getPractoAccountId() != $authenticatedPractoAccountId) {
            throw new HttpException(Codes::HTTP_FORBIDDEN, "Unauthorised access" );
        }

        return $result;
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
