<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use ConsultBundle\Entity\Question;
use Symfony\Component\Validator\ValidatorInterface;
use ConsultBundle\Entity\QuestionImage;
use ConsultBundle\Entity\QuestionBookmark;

/**
 * Question Manager
 */
class QuestionManager extends BaseManager
{

    protected $questionImageManager;
    protected $questionBookmarkManager;

    /**
     * Constructor
     *
     * @param Doctrine                 $doctrine           - Doctrine
     * @param ValidatorInterface       $validator          - Validator
     */
    public function __construct(
        QuestionImageManager $questionImageManager, QuestionBookmarkManager $questionBookmarkManager )
    {
        $this->questionImageManager = $questionImageManager;
        $this->questionBookmarkManager = $questionBookmarkManager;

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

        if (array_key_exists('bookmark', $requestParams)) {
            if ($requestParams['bookmark']) {
                $data['bookmark'] = $requestParams['bookmark'];
                $data['practo_account_id'] = $requestParams['practo_account_id'];

                $questionBookmark = new questionBookmark;
                $questionBookmark->setQuestion($question);
                try {
                    $this->questionBookmarkManager->updateFields($questionBookmark, $data);
                    $question->addBookmark($questionBookmark);
                } catch (ValidationError $e) {
                    @$errors['bookmark'][$index + 1] = json_decode($e->getMessage(), true);
                }
                unset($requestParams['bookmark']);
            } else {
                //todo
            }
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
        $this->helper->persist($question, "true");


        return $question;
    }

    /**
     * Load Question By Id
     *
     * @param integer $questionId - Question Id
     *
     * @return Question
     */
    public function load($questionId)
    {

        $question = $this->helper->loadById($questionId, ConsultConstants::$QUESTION_ENTITY_NAME);


        if (is_null($question)) {
            return null;
        }

        return $question;
    }

    /**
     * Load Questions By UserId
     *
     * @param integer $practoId - PractoId
     *
     * @return array of Question
     */
    public function loadByUserID($practoId)
    {

        $questionList = $this->helper->getRepository(
                                    ConsultConstants::$QUESTION_ENTITY_NAME)->findBy(
                                                                        array('practoAccountId' => $practoId,
                                                                              'softDeleted' => 0));
        if (is_null($questionList)) {
            return null;
        }

        return $questionList;
    }

    /**
     * Load Question By Id
     *
     * @param integer $questionId - Question Id
     *
     * @return Question
     */
    public function loadAll()
    {

        $question = $this->helper->loadAll(ConsultConstants::$QUESTION_ENTITY_NAME);


        if (is_null($question)) {
            return null;
        }

        return $question;
    }
}
