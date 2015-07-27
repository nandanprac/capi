<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 10/06/15
 * Time: 11:27
 */

namespace ConsultBundle\Mapper;

use ConsultBundle\Entity\DoctorQuestion;
use ConsultBundle\Entity\Question;
use ConsultBundle\Response\BasicDoctorQuestionResponseObject;
use ConsultBundle\Response\BasicPatientInfoResponse;
use ConsultBundle\Response\BasicQuestionResponseObject;
use ConsultBundle\Response\DetailQuestionResponseObject;
use ConsultBundle\Response\ReplyResponseObject;
use ConsultBundle\Entity\DoctorEntity;

/**
 * Class QuestionMapper
 *
 * @package ConsultBundle\Mapper
 */
class QuestionMapper
{
    /**
     * @param \ConsultBundle\Entity\Question                      $questionEntity
     * @param \ConsultBundle\Response\BasicQuestionResponseObject $questionResponse
     *
     * @return \ConsultBundle\Response\BasicQuestionResponseObject
     */
    public static function mapFromQuestion(Question $questionEntity, BasicQuestionResponseObject $questionResponse = null)
    {
        if ($questionResponse == null) {
            $questionResponse = new BasicQuestionResponseObject();
        }

        if (!empty($questionEntity)) {
            self::mapBasicQuestion($questionEntity, $questionResponse);
        }

        return $questionResponse;
    }

    /**
     * @param array $questionList
     *
     * @return array
     */
    public static function mapQuestionList(array $questionList)
    {
        $questionResponseList =  array();
        if (!empty($questionList)) {
            foreach ($questionList as $questionArray) {
                $questionResponse = new BasicQuestionResponseObject();
                $questionResponse->setAttributes($questionArray['question']);
                $questionResponse->setBookmarkCount($questionArray['bookmarkCount']);
                $questionResponseList[] = $questionResponse;
            }

        }

        return $questionResponseList;

    }

    /**
     * @param \ConsultBundle\Entity\Question                       $questionEntity
     * @param \ConsultBundle\Response\DetailQuestionResponseObject $questionResponseObject
     *
     * @return \ConsultBundle\Response\DetailQuestionResponseObject
     */
    public static function mapDetailedQuestion(Question $questionEntity, DetailQuestionResponseObject $questionResponseObject = null)
    {
        if (empty($questionResponseObject)) {
            $questionResponseObject =  new DetailQuestionResponseObject($questionEntity);
        }

        if (!empty($questionEntity)) {
            $replies = array();
            foreach ($questionEntity->getDoctorQuestions() as $doctorQuestionEntity) {
                /**
                 * @var DoctorQuestion $doctorQuestionEntity
                 */
                if (!empty($doctorQuestionEntity)||$doctorQuestionEntity->isSoftDeleted()||empty($doctorQuestionEntity->getDoctorReplies()
                ||$doctorQuestionEntity->getDoctorReplies()->isSoftDeleted())
                ) {
                    $reply = new ReplyResponseObject();
                    self::mapDoctorQuestion($doctorQuestionEntity, $reply);
                    $replies[] = $reply;

                }
            }

            $questionResponseObject->setReplies($replies);

        }

        return $questionResponseObject;
    }

    /**
     * @param array $doctorQuestionList
     *
     * @return array
     */
    public static function mapDoctorQuestionList(array $doctorQuestionList)
    {
        $doctorQuestionResponseList =  array();

        if (!empty($doctorQuestionList)) {
            foreach ($doctorQuestionList as $questionArray) {
                $doctorQuestionResponse = new BasicDoctorQuestionResponseObject($questionArray['doctorQuestion']);
                $doctorQuestionResponse->setVotes($questionArray['votes']);
                $doctorQuestionResponse->setRating($questionArray['rating']);
                $doctorQuestionResponse->setBookmarkCount($questionArray['bookmarkCount']);
                $doctorQuestionResponseList[] = $doctorQuestionResponse;
            }

        }

        return $doctorQuestionResponseList;

    }


    /**
     * @param DetailQuestionResponseObject $question
     * @return array Response;
     */
    public static function mapToModerationArray(DetailQuestionResponseObject $question)
    {
        $response=array();
        $response['replies']=array();
        $patientInfo = new BasicPatientInfoResponse();
        $patientInfo = $question->getPatientInfo();

        $patientArr=array();
        $patientArr['age']=$patientInfo->getAge();
        $patientArr['id']=$patientInfo->getId();

        foreach ($question->getReplies() as $replies) {
            $replyArr=array();
            $docInfo=$replies->getDoctor();
            $docInfoArr=array();
            $docInfoArr['name']=$docInfo->getname();
            $docInfoArr['speciality']=$docInfo->getspeciality();
            $replyArr['doctor']=$docInfoArr;
            $replyArr['text']=$replies->gettext();
            $replyArr['rating']=$replies->getrating();
            $replyArr['flagCode']=$replies->getflagText();
            $replyArr['replyTime']=$replies->getCreatedAt();
            array_push($response['replies'], $replyArr);
        }

        //        $patientArr['email']=$patientInfo->getEmail();

        //encode info object if needed

        $response['patientInfo']=$patientArr;
        $response['isBookmarked']=$question->isIsBookmarked();
        $response['comments']=$question->getComments();
        $response['commentsCount']=$question->getCommentsCount();
        $response['subject']=$question->getSubject();
        $response['text']=$question->getText();
        $response['speciality']=$question->getSpeciality();
        $response['state']=$question->getState();
        $response['viewCount']=$question->getViewCount();
        $response['shareCount']=$question->getShareCount();
        $response['bookmarkCount']=$question->getBookmarkCount();
        $response['viewedAt']=$question->getViewedAt();
        $response['id']=$question->getId();
        $response['createdAt']=$question->getCreatedAt();
        $response['modifiedAt']=$question->getModifiedAt();

        return $response;
    }

    private static function mapBasicQuestion(Question $questionEntity, BasicQuestionResponseObject $question)
    {
        $question->setId($questionEntity->getId());
        $question->setSpeciality($questionEntity->getSpeciality());
        $question->setViewCount($questionEntity->getViewCount());
        $question->setShareCount($questionEntity->getSpeciality());
        $question->setSubject($questionEntity->getSubject());
        $question->setText($questionEntity->getText());
        $question->setModifiedAt($questionEntity->getModifiedAt());
        $question->setCreatedAt($questionEntity->getCreatedAt());
        $question->setCreatedAt($questionEntity->getCreatedAt());
    }
}
