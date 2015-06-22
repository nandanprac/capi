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


class RetrieveDoctorProfileUtil {

    /**
     * @var Client $client
     */
    private $client;

    public function retrieveDoctorProfile($practoAccntId = 5)
    {
        $this->client = new Client();

        $params['index'] = 'fabric_search';
        $params['type']  = 'search';
        $params['_source']  = array('doctor_id', 'doctor_name', 'practo_account_id', 'specialties.specialty', 'profile_picture');

        $params['body']['query']['match']['practo_account_id'] = $practoAccntId;
       // $params['body']['query']['bool']['must']['query_string']['default_field']  = 'search.specialties.specialty';
        //$params['body']['query']['bool']['must']['query_string']['query']  = $speciality;

        //$params['body']['query']['bool']['must']['query_string']['default_field']  = 'search.city';
        //$params['body']['query']['bool']['must']['query_string']['query']  = $city;
        //$params['body']['from']  = 0;
        //$params['body']['size']  = 100;
        $results = $this->client->search($params);


        if(count($results['hits']['hits']) === 0 )
        {
            return null;
        }

<<<<<<< HEAD
        $doc = null;

        foreach ($results['hits']['hits'] as $result) {
=======
        foreach($results['hits']['hits'] as $result)
        {
>>>>>>> master
            $doc = $this->populateDoctorObject($result['_source']);

        }

<<<<<<< HEAD

        if (is_null($doc)) {
=======
        if(is_null($doc))
        {
>>>>>>> master
            return null;
        }

        return $doc;


    }


<<<<<<< HEAD
    /**
     * @param \ConsultBundle\Response\DetailQuestionResponseObject $questionResponseObject
     */
    public function retrieveDoctorProfileForQuestionResponse(DetailQuestionResponseObject $questionResponseObject)
=======
    public function retrieveDoctorProfileForQuestion(Question $question)
>>>>>>> master
    {

        $replies = $questionResponseObject->getReplies();

<<<<<<< HEAD
        /**
         * @var ReplyResponseObject $reply
         */
        foreach ($replies as $reply) {
            $doctorId = $reply->getDoctorId();
            $doc = $this->retrieveDoctorProfile($doctorId);
            $reply->setDoctor($doc);
        }
=======
        foreach($doctorQuestions as $doctorQuestion)
        {

            $doctorId = $doctorQuestion->getPractoAccountId();
>>>>>>> master


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


<<<<<<< HEAD



    /**
     * @param array $docArr
     *
     * @return \ConsultBundle\Entity\DoctorEntity|null
     */
=======
>>>>>>> master
    private function populateDoctorObject(array $docArr)
    {

        //var_dump("123");

        if(is_null($docArr))
        {
            return null;
        }



        $doc = new DoctorEntity();


        if(array_key_exists('doctor_name', $docArr))
        {

            $doc->setName($docArr['doctor_name']);
        }

        if(array_key_exists('profile_picture', $docArr))
        {
            $doc->setProfilePicture($docArr['profile_picture']);
        }

<<<<<<< HEAD
        if (array_key_exists('specialties', $docArr)) {
            foreach ($docArr['specialties'] as $specialties) {
                if (array_key_exists('specialty', $docArr['specialties'])) {
                }
                $doc->setSpeciality($specialties['specialty']);
=======
        if(array_key_exists('specialties', $docArr))
        {

            foreach($docArr['specialties'] as $specialties)
            {
                $doc->setSpecialty($specialties['specialty']);
>>>>>>> master
            }
        }


        return $doc;


    }

}