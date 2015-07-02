<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\Conversation;
use ConsultBundle\Entity\ConversationImage;
use ConsultBundle\Entity\Question;
use ConsultBundle\Entity\QuestionImage;
use ConsultBundle\Utility\FileUploadUtil;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Question Image Manager
 */
class QuestionImageManager extends BaseManager
{
    /**
     * @var FileUploadUtil
     */
    protected $fileUploadUtil;

    private $maxNumQsImage;

    private $maxNumCnvImage;

    /**
     * @param \ConsultBundle\Utility\FileUploadUtil $fileUploadUtil
     * @param \ConsultBundle\Manager\int            $maxNumQsImage
     * @param \ConsultBundle\Manager\int            $maxNumCnvImage
     */
    public function __construct(FileUploadUtil $fileUploadUtil, $maxNumQsImage, $maxNumCnvImage)
    {
        $this->fileUploadUtil = $fileUploadUtil;
        $this->maxNumCnvImage = $maxNumCnvImage;
        $this->maxNumQsImage = $maxNumQsImage;
    }


    /**
     * @param int     $questionId
     * @param FileBag $fileBag
     */
    public function add($questionId, FileBag $fileBag)
    {
        if ($fileBag->count() > $this->maxNumQsImage) {
            throw new HttpException(Codes::HTTP_BAD_REQUEST, "Too many Images");
        }

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
    /**
     * @param int     $conversationId
     * @param FileBag $fileBag
     */
    public function addConversationImage($conversationId, FileBag $fileBag)
    {
        if ($fileBag->count() > $this->maxNumCnvImage) {
            throw new HttpException(Codes::HTTP_BAD_REQUEST, "Too many Images");
        }

        $urls = $this->fileUploadUtil->add($fileBag, "private".$conversationId);
        /**
         * @var Conversation $conversation
         */
        $conversation = $this->helper->loadById($conversationId, ConsultConstants::CONVERSATION_ENTITY_NAME);
        $conversationImages = new ArrayCollection();

        foreach ($urls as $url) {
            $conversationImage = new ConversationImage();
            $conversationImage->setUrl($url);
            $conversationImage->setConversation($conversation);
            $conversationImages->add($conversationImage);
        }

        $conversation->setImages($conversationImages);

        if ($conversationImages->count() > 0) {
            $this->helper->persist($conversation, true);
        }
    }
}
