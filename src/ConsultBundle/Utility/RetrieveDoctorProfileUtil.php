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

        foreach($results['hits']['hits'] as $result)
        {
            $doc = $this->populateDoctorObject($result['_source']);

        }
        //var_dump($doc);
        //die;

        //var_dump($results);die;

      // $doc = $this->populateDoctoreObject($results['hits']['hits']['0']['_source']);

        //var_dump($doc);die;

        return $doc;


    }


    public function retrieveDoctorProfileForQuestion(Question $question)
    {
        $doctorQuestions = $question->getDoctorQuestions();

        foreach($doctorQuestions as $doctorQuestion)
        {
            $doctor_id = $doctorQuestion->getPractoAccountId();
            $doc = $this->retrieveDoctorProfile($doctor_id);

            $doctorQuestion->setDoctor($doc);
        }
    }


    private function populateDoctorObject(array $docArr)
    {


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

        if(array_key_exists('specialties', $docArr))
        {

            foreach($docArr['specialties'] as $specialties)
            {
                $doc->setSpecialty($specialties['specialty']);
            }
        }

        return $doc;


    }

}