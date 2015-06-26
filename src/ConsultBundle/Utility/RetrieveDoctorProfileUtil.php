<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 22/05/15
 * Time: 16:22
 */

namespace ConsultBundle\Utility;

use ConsultBundle\Entity\DoctorConsultSettings;
use ConsultBundle\Entity\DoctorEntity;
use ConsultBundle\Entity\Question;
use ConsultBundle\Manager\DoctorManager;
use ConsultBundle\Response\DetailQuestionResponseObject;
use ConsultBundle\Response\ReplyResponseObject;

/**
 * Class RetrieveDoctorProfileUtil
 *
 * @package ConsultBundle\Utility
 */
class RetrieveDoctorProfileUtil
{
    private $host = "http://localhost:9200";

    private $index = 'fabric_search';

    /**
     * @var Client $client
     */
    private $client;

    /**
     * @var DoctorManager $doctorManager
     */
    private $doctorManager;

    /**
     * @param \ConsultBundle\Manager\DoctorManager $doctorManager
     */
    public function __construct(DoctorManager $doctorManager)
    {

        $this->doctorManager = $doctorManager;
    }

    /**
     * @param int $practoAccntId
     *
     * @return \ConsultBundle\Entity\DoctorEntity|null
     */
    public function retrieveDoctorProfile($practoAccntId = 5)
    {
        /**
         * @var DoctorConsultSettings $docEntity
         */
        $docEntity = $this->doctorManager->getConsultSettingsByPractoAccountId($practoAccntId);

        $doc = new DoctorEntity();
        $doc->setFabricId($docEntity->getFabricDoctorId());
        $doc->setName($docEntity->getName());
        $doc->setProfilePicture($docEntity->getProfilePicture());
        $doc->setSpeciality($docEntity->getSpeciality());

        return $doc;


    }





    /**
     * @param int $practoAccntId
     *
     * @return \ConsultBundle\Entity\DoctorEntity|null
     */
    public function retrieveDoctorProfileOld($practoAccntId = 5)
    {
        //$this->client = new Client();

        $params['index'] = $this->index;
        $params['type']  = 'search';
        $params['_source']  = array('doctor_id', 'doctor_name', 'practo_account_id', 'specialties.specialty', 'profile_picture');

        $params['body']['query']['match']['practo_account_id'] = $practoAccntId;

        $results = $this->client->search($params);


        if (count($results['hits']['hits']) === 0) {
            return null;
        }

        $doc = null;

        foreach ($results['hits']['hits'] as $result) {
            $doc = $this->populateDoctorObject($result['_source']);

        }


        if (is_null($doc)) {
            return null;
        }

        return $doc;


    }


    /**
     * @param \ConsultBundle\Response\DetailQuestionResponseObject $questionResponseObject
     */
    public function retrieveDoctorProfileForQuestionResponse(DetailQuestionResponseObject $questionResponseObject)
    {

        $replies = $questionResponseObject->getReplies();

        /**
         * @var ReplyResponseObject $reply
         */
        foreach ($replies as $reply) {
            $doctorId = $reply->getDoctorId();
            $doc = $this->retrieveDoctorProfile($doctorId);
            $reply->setDoctor($doc);
        }


    }

    /**
     * @deprecated
     * @param \ConsultBundle\Entity\Question $question
     *
     * @return null
     */
    public function retrieveDoctorProfileForQuestion(Question $question)
    {

        return $question;

    }





    /**
     * @param array $docArr
     *
     * @return \ConsultBundle\Entity\DoctorEntity|null
     */
    private function populateDoctorObject(array $docArr)
    {



        if (is_null($docArr)) {
            return null;
        }



        $doc = new DoctorEntity();


        if (array_key_exists('doctor_name', $docArr)) {
            $doc->setName($docArr['doctor_name']);
        }

        if (array_key_exists('profile_picture', $docArr)) {
            $doc->setProfilePicture($docArr['profile_picture']);
        }

        if (array_key_exists('specialties', $docArr)) {
            foreach ($docArr['specialties'] as $specialties) {
                if (array_key_exists('specialty', $docArr['specialties'])) {
                }
                $doc->setSpeciality($specialties['specialty']);
            }
        }


        return $doc;


    }
}
