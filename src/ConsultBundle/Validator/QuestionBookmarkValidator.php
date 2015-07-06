<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 17:44
 */

namespace ConsultBundle\Validator;

use ConsultBundle\Entity\Question;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * Question Bookmark Validator
 */
class QuestionBookmarkValidator extends BaseValidator
{
    /**
     * Constructor
     *
     * @param ValidatorInterface $validator - Validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct($validator);
    }


    /**
     * @param Question $question        - question object
     * @param integer  $practoAccountId - User's id
     * @return bool
     */
    public function checkUniqueness($question, $practoAccountId)
    {
        foreach ($question->getBookmarks() as $bookmark) {
            if ($bookmark->getPractoAccountId() == $practoAccountId) {
                return true;
            }
        }

        return false;
    }
}
