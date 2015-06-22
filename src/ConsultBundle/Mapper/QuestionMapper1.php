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
use ConsultBundle\Response\BasicQuestionResponseObject;
use ConsultBundle\Response\DetailQuestionResponseObject;
use ConsultBundle\Response\ReplyResponseObject;

/**
 * Class QuestionMapper1
 *
 * @package ConsultBundle\Mapper
 */
class QuestionMapper1
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
        $newQuestion=new Question();
        if (!empty($questionList)) {
            foreach ($questionList as $questionArray) {
               // $qb= $this->getDoctrine()->getRepository('ConsultBundle:Question')->createQueryBuilder('1');
               // $qb->select('1')->where('q.id'==$questionArray('id'));
              //  $query = $qb->getQuery();
             //   $myQuestion= $query->getResult;
                $questionResponse = new DetailQuestionResponseObject($myQuestion);
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

    private static function mapDoctorQuestion(DoctorQuestion $doctorQuestionEntity, ReplyResponseObject $reply)
    {

    }
}
