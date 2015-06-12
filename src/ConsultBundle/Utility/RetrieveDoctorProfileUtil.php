<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 22/05/15
 * Time: 16:22
 */

namespace ConsultBundle\Utility;

use ConsultBundle\Entity\DoctorEntity;
use ConsultBundle\Entity\Question;
use ConsultBundle\Response\DetailQuestionResponseObject;
use ConsultBundle\Response\ReplyResponseObject;
use Elasticsearch\Client;

/**
 * Class RetrieveDoctorProfileUtil
 *
 * @package ConsultBundle\Utility
 */
class RetrieveDoctorProfileUtil
{

    /**
     * @var Client $client
     */
    private $client;

    /**
     * @param int $practoAccntId
     *
     * @return \ConsultBundle\Entity\DoctorEntity|null
     */
    public function retrieveDoctorProfile($practoAccntId = 5)
    {
        $this->client = new Client();

        $params['index'] = 'fabric_search';
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
                $doc->setSpeciality($specialties['specialty']);
            }
        }

        return $doc;


    }
}
