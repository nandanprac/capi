<?php

namespace ConsultBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use ConsultBundle\Entity\Question;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

/**
 * Question Manager
 */
class QuestionManager
{
    protected $doctrine;
    protected $validator;

    /**
     * Constructor
     *
     * @param Doctrine                 $doctrine           - Doctrine
     * @param ValidatorInterface       $validator          - Validator
     */
    public function __construct(Doctrine $doctrine, ValidatorInterface $validator)
    {
        $this->doctrine = $doctrine;
        $this->validator = $validator;
    }

    /**
     * Update Fields
     *
     * @param Question $question     - PatientGrowth
     * @param array         $requestParams     - Request parameters
     *
     * @return null
     */
    public function updateFields($question, $requestParams)
    {
        $errors = array();
        $question->setAttributes($requestParams);
        $question->setModifiedAt(new \DateTime('now'));

        $validationErrors = $this->validator->validate($question);
        if (0 < count($validationErrors)) {
            foreach ($validationErrors as $validationError) {
              $pattern = '/([a-z])([A-Z])/';
              $replace = function ($m) {
                  return $m[1] . '_' . strtolower($m[2]);
              };
              $attribute = preg_replace_callback($pattern, $replace, $validationError->getPropertyPath());
              @$errors[$attribute][] = $validationError->getMessage();
            }
        }

        if (0 < count($errors)) {
            throw new ValidationError($errors);
        }

        return;
    }

    /**
     * Add New Patient Growth
     *
     * @param array $requestParams
     *
     * @return PatientNote
     */
    public function add($requestParams)
    {
        $question = new Question();
        $question->setCreatedAt(new \DateTime('now'));
        $question->setSoftDeleted(false);

        $this->updateFields($question, $requestParams);
        $em = $this->doctrine->getManager();
        $em->persist($question);
        $em->flush();

        return $question;
    }
}
