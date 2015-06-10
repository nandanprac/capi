<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 10/06/15
 * Time: 11:27
 */

namespace ConsultBundle\Mapper;

use ConsultBundle\Entity\Question;
use ConsultBundle\Response\BasicQuestionResponseObject;

/**
 * Class BasicQuestionMapper
 *
 * @package ConsultBundle\Mapper
 */
class BasicQuestionMapper
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

        //var_dump(json_encode($questionResponseList, true));die;
        return $questionResponseList;

    }

    private static function mapBasicQuestion(Question $questionEntity, BasicQuestionResponseObject $question)
    {
        $question->setId($questionEntity->getId());
        $question->setSpecialty($questionEntity->getSpeciality());
        $question->setViewCount($questionEntity->getViewCount());
        $question->setShareCount($questionEntity->getSpeciality());
        $question->setSubject($questionEntity->getSubject());
        $question->setText($questionEntity->getText());
        $question->setModifiedAt($questionEntity->getModifiedAt());
        $question->setCreatedAt($questionEntity->getCreatedAt());
        $question->setCreatedAt($questionEntity->getCreatedAt());
    }
}