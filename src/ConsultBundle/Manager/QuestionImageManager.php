<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\Question;
use ConsultBundle\Entity\QuestionImage;
use ConsultBundle\Utility\FileUploadUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\FileBag;

/**
 * Question Image Manager
 */
class QuestionImageManager extends BaseManager
{
    /**
     * @var FileUploadUtil
     */
    protected $fileUploadUtil;

    /**
     * @param \ConsultBundle\Utility\FileUploadUtil $fileUploadUtil
     */
    public function __construct(FileUploadUtil $fileUploadUtil)
    {
        $this->fileUploadUtil = $fileUploadUtil;
    }


    /**
     * @param int     $questionId
     * @param FileBag $fileBag
     */
    public function add($questionId, FileBag $fileBag)
    {

        $urls = $this->fileUploadUtil->add($fileBag, $questionId);
        $question = $this->helper->loadById($questionId, ConsultConstants::QUESTION_ENTITY_NAME);
        $questionImages = new ArrayCollection();

        foreach ($urls as $url) {
            $questionImage = new QuestionImage();
            $questionImage->setUrl($url);
            $questionImage->setQuestion($question);
            $questionImages->add($questionImage);
        }

        $question->setImages($questionImages);

        if ($questionImages->count() > 0) {
            $this->helper->persist($question, true);
        }
    }
}
