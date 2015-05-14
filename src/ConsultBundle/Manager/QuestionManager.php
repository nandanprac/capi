<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use ConsultBundle\Entity\Question;
use ConsultBundle\Entity\QuestionImage;
use ConsultBundle\Entity\QuestionBookmark;
use ConsultBundle\Manager\ValidationError;

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

        try {
            $this->validator->validate($question);
        } catch(ValidationError $e) {
            throw new ValidationError($e->getMessage());
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
        $this->helper->persist($question, 'true');

        return $question;
    }

    public function patch($question, $requestParams)
    {
        if (array_key_exists('question_id', $requestParams)) {
            unset($requestParams['question_id']);
        }
        if (array_key_exists('_method', $requestParams)) {
            unset($requestParams['_method']);
        }
        $this->updateFields($question, $requestParams);
        $this->helper->persist($question, 'true');

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
     * Load all questions based on filters
     *
     * @param request parameters
     *
     * @return Question
     */
    public function loadAll()
    {
        $questionList = $this->helper->loadAll(ConsultConstants::$QUESTION_ENTITY_NAME);

        if (is_null($questionList)) {
            return null;
        }

        return $questionList;
    }

    public function loadByFilters($request)
    {
        if (array_key_exists('practo_account_id', $request) and array_key_exists('bookmark', $request)) {
            $questionList = $this->loadByAccId($request['practo_account_id'], $request['bookmark']);
        }
        if (array_key_exists('modified_at', $request)) {
            $from = new \DateTime($request['modified_at']);
            $from->format('Y-m-d H:i:s');
            $questionList = $this->loadByModifiedTime($from);
        }

        $from = new \DateTime('now');
        $from->sub(new \DateInterval('P1M'))->format('Y-m-d H:i:s');

        if (array_key_exists('state', $request)) {
            $limit = 100;
            $offset = 0;
            if (array_key_exists('limit', $request)) {
                $limit = $request['limit'];
            }
            if (array_key_exists('offset', $request)) {
                $offset = $request['offset'];
            }
            if (array_key_exists('modified_at', $request)) {
                $from = new \DateTime($request['modified_at']);
                $from->format('Y-m-d H:i:s');
            }
            $questionList = $this->loadFeed($from, $request['state'], $limit, $offset);
        }
        if (array_key_exists('category', $request)) {
            $limit = 100;
            $offset = 0;
            if (array_key_exists('limit', $request)) {
                $limit = $request['limit'];
            }
            if (array_key_exists('offset', $request)) {
                $offset = $request['offset'];
            }
            $questionList = $this->loadByCategory($request['category'], $limit, $offset);
        }
        return $questionList;
    }

    private function loadByAccId($practoAccountId, $bookmark)
    {
        $questionList = $this->helper->getRepository(
            ConsultConstants::$QUESTION_ENTITY_NAME)->findBy(
            array('practoAccountId' => $practoAccountId));
        $bookmarkList = $this->helper->getRepository(
            ConsultConstants::$QUESTION_BOOKMARK_ENTITY_NAME)->findBy(
            array('practoAccountId' => $practoAccountId));

        if (is_null($questionList) and is_null($bookmarkList)) {
            return null;
        }
        if ($bookmark == 0)
            return $questionList;
        else if ($bookmark == 1)
            return $bookmarkList;
        else if ($bookmark == 2)
            return array_merge($questionList, $bookmarkList);
    }

    private function loadByModifiedTime($modifiedAt)
    {
        $er =  $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
        $questionList = $er->findQuestionsByModifiedTime($modifiedAt);

        return  $questionList;
    }

    private function loadFeed($modifiedAt, $state, $limit, $offset)
    {
        $er = $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
        $questionList = $er->findQuestionsByState($modifiedAt, $state, $limit, $offset);

        return $questionList;
    }

    private function loadByCategory($category, $limit, $offset)
    {
        $er = $this->helper->getRepository(ConsultConstants::$QUESTION_ENTITY_NAME);
        $questionList = $er->findQuestionsByCategory($category, $limit, $offset);

        return $questionList;
    }

}