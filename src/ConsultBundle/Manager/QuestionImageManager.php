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
     * Update Fields
     *
     * @param QuestionImage $questionImage  - Question Image
     * @param array         $data           - Array Parameters
     *
     * @return null
     */
    public function updateFields($questionImage, $data)
    {
        $errors = array();
        $questionImage->setAttributes($data);

        $validationErrors = $this->validate($questionImage);

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


    public function add(Question $question, FileBag $fileBag)
    {
        //var_dump($fileBag->count());
        //die;
       $urls = $this->fileUploadUtil->add($fileBag, $question->getId());
       //var_dump($urls);die;
        $questionImages = new ArrayCollection();

        foreach($urls as $url)
        {
             $questionImage = new QuestionImage();
            $questionImage->setUrl($url);
            $questionImage->setQuestion($question);
            //$this->helper->persist(questionImage);
            $questionImages->add($questionImage);
        }

        //var_dump("123"); die;
        $question->setImages($questionImages);

        if($questionImages->count() > 0)
        {
            $this->helper->persist($question, true);
        }
    }



}
