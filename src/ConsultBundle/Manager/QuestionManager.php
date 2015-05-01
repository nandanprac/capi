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
use ConsultBundle\Entity\QuestionImage;

/**
 * Question Manager
 */
class QuestionManager
{
    protected $doctrine;
    protected $validator;
    protected $questionImageManager;

    /**
     * Constructor
     *
     * @param Doctrine                 $doctrine           - Doctrine
     * @param ValidatorInterface       $validator          - Validator
     */
    public function __construct(Doctrine $doctrine, ValidatorInterface $validator,
        QuestionImageManager $questionImageManager)
    {
        $this->doctrine = $doctrine;
        $this->validator = $validator;
        $this->questionImageManager = $questionImageManager;
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

        if (array_key_exists('images', $requestParams)) {
            $images = $requestParams['images'];
            if (!is_array($images)) {
                @$errors['images'][] = 'This must be an array';
            } else {
                if ($question->getImages()) {
                    foreach ($question->getImages() as $image) {
                        $this->em->remove($image);
                    }
                }
                $question->clearImages();
                foreach ($images as $index => $imageData) {
                    if (!is_array($imageData)) {
                        @$errors['images'][$index + 1] = 'This must be an array';
                    } else {
                        if (array_key_exists('id', $imageData)) {
                            unset($imageData['id']);
                        }
                        $questionImage = new QuestionImage;
                        $questionImage->setQuestion($question);
                        try {
                            $this->questionImageManager->updateFields($questionImage, $imageData);
                            $question->addImage($questionImage);
                        } catch (ValidationError $e) {
                            @$errors['images'][$index + 1] = json_decode($e->getMessage(), true);
                        }
                    }
                }
            }
            unset($requestParams['images']);
        }

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
