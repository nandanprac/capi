<?php

namespace ConsultBundle\Manager;
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

    public function __construct(FileUploadUtil $fileUploadUtil)
    {
        $this->fileUploadUtil = $fileUploadUtil;
    }


    /**
     * @param Question $question
     * @param FileBag $fileBag
     */
    public function add(Question $question, FileBag $fileBag)
    {

       $urls = $this->fileUploadUtil->add($fileBag, $question->getId());
        $questionImages = new ArrayCollection();

        foreach($urls as $url)
        {
             $questionImage = new QuestionImage();
            $questionImage->setUrl($url);
            $questionImage->setQuestion($question);
            $questionImages->add($questionImage);
        }

        $question->setImages($questionImages);

        if($questionImages->count() > 0)
        {
            $this->helper->persist($question, true);
        }
    }



}
