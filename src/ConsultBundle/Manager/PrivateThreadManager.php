<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Entity\BaseEntity;
use ConsultBundle\Entity\DoctorEntity;
use ConsultBundle\Repository\PrivateThreadRepository;
use FOS\RestBundle\Util\Codes;
use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\UserInfo;
use ConsultBundle\Response\DetailPatientInfoResponse;
use ConsultBundle\Utility\RetrieveDoctorProfileUtil;
use ConsultBundle\Utility\RetrieveUserProfileUtil;
use ConsultBundle\Utility\Utility;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use ConsultBundle\Entity\PrivateThread;
use ConsultBundle\Entity\Conversation;
use ConsultBundle\Manager\ValidationError;
use ConsultBundle\Queue\AbstractQueue as Queue;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Private Thread Manager
 */
class PrivateThreadManager extends BaseManager
{

    protected $userManager;
    protected $queue;
    protected $retrieveUserProfileUtil;
    protected $retrieveDoctorProfileUtil;
    protected $doctorManager;
    private $questionImageManager;


    /**
     * @param \ConsultBundle\Manager\UserManager               $userManager
     * @param \ConsultBundle\Queue\AbstractQueue               $queue
     * @param \ConsultBundle\Utility\RetrieveUserProfileUtil   $retrieveUserProfileUtil
     * @param \ConsultBundle\Utility\RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil
     * @param \ConsultBundle\Manager\DoctorManager             $doctorManager
     * @param \ConsultBundle\Manager\QuestionImageManager      $questionImageManager
     */
    public function __construct(
        UserManager $userManager,
        Queue $queue,
        RetrieveUserProfileUtil $retrieveUserProfileUtil,
        RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil,
        DoctorManager $doctorManager,
        QuestionImageManager $questionImageManager
    ) {
        $this->userManager = $userManager;
        $this->queue = $queue;
        $this->retrieveUserProfileUtil = $retrieveUserProfileUtil;
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
        $this->doctorManager = $doctorManager;
        $this->questionImageManager = $questionImageManager;
    }


    /**
     * @param array                                     $requestParams
     * @param int                                       $practoAccountId
     * @param \Symfony\Component\HttpFoundation\FileBag $files
     * @param null                                      $profileToken
     *
     * @return Object
     * @throws \ConsultBundle\Manager\ValidationError
     */
    public function add($requestParams, $practoAccountId, FileBag $files, $profileToken = null)
    {

        /**
         * @var PrivateThreadRepository $er
         */
        $er = $this->helper->getRepository(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME);
        $privateThreadId = array_key_exists('private_thread_id', $requestParams) ? $requestParams['private_thread_id'] : null;

        if (!empty($privateThreadId)) {
            $privateThread = $this->helper->loadById($privateThreadId, ConsultConstants::PRIVATE_THREAD_ENTITY_NAME);

            if (empty($privateThread)) {
                @$error['error'] = 'Private thread with the provided id could not be found';
                throw new ValidationError($error);
            }

            if (array_key_exists('is_doc_reply', $requestParams) && Utility::toBool($requestParams['is_doc_reply'])) {
                if ($privateThread->getDoctorId() != $practoAccountId) {
                    throw new HttpException(Codes::HTTP_FORBIDDEN, 'You are not allowed to answer this question');
                }
            } else {
                if(!$this->userManager->checkConsultEnabled($practoAccountId)) {
                    throw new HttpException(Codes::HTTP_FORBIDDEN, "User has not consented to use Consult");
                }
                if ($privateThread->getUserInfo()->getPractoAccountId() != $practoAccountId) {
                    throw new HttpException(Codes::HTTP_FORBIDDEN, 'You are not allowed to ask any question here');
                }
                if ($er->checkFollowUpCount($practoAccountId, $privateThread) <= 0) {
                    @$error['error'] = 'You have exhausted your follow up questions limit';
                    throw new ValidationError($error);
                }
                /*   $lastConversation = $this->helper->getRepository(ConsultConstants::CONVERSATION_ENTITY_NAME)
                                      ->findBy(array(), array('createdAt' => 'DESC'), 1, 0);
                 if (!empty($lastConversation)) {
                      if (!Utility::toBool($lastConversation[0]->getIsDocReply())) {
                          @$error['error'] = 'Wait for doctor\'s reply before following up again';
                          throw new ValidationError($error);
                      }
                  }*/

                $userInfoParams = array();
                if (array_key_exists('user_info', $requestParams)) {
                    $userInfoParams = $requestParams['user_info'];
                    unset($requestParams['user_info']);
                }
                $userInfoParams['practo_account_id'] = $practoAccountId;
                $userEntry = $this->userManager->add($userInfoParams, $profileToken);
                $privateThread->setUserInfo($userEntry);
            }

            $conversation = new Conversation();
            $conversation->setPrivateThread($privateThread);
            $privateThread->setModifiedAt(new \DateTime('now'));

        } else {
            if (!array_key_exists('reply_id', $requestParams)) {
                @$error['error'] = 'Either id of previous reply or id of the private thread is required';
                throw new ValidationError($error);
            }
            if ($er->privateThreadExists($practoAccountId)) {
                @$error['error'] = 'You have already started a private thread. Cannot start another';
                throw new ValidationError($error);
            }

            $replyId = $requestParams['reply_id'];
            $reply = $this->helper->loadById($replyId, ConsultConstants::DOCTOR_REPLY_ENTITY_NAME);
            if (empty($reply)) {
                @$error['error'] = 'Reply with the provided id could not be found';
                throw new ValidationError($error);
            }

            $privateThread = new PrivateThread();

            $userInfoParams = array();
            if (array_key_exists('user_info', $requestParams)) {
                $userInfoParams = $requestParams['user_info'];
                unset($requestParams['user_info']);
            }
            $userInfoParams['practo_account_id'] = $practoAccountId;
            $userEntry = $this->userManager->add($userInfoParams, $profileToken);
            $privateThread->setUserInfo($userEntry);

            $privateThread->setQuestion($reply->getDoctorQuestion()->getQuestion());
            $privateThread->setDoctorId($reply->getDoctorQuestion()->getPractoAccountId());
            $subject =  $reply->getDoctorQuestion()->getQuestion()->getSubject();
            $privateThread->setSubject($subject);

            $conversation = new Conversation();
            $conversation->setPrivateThread($privateThread);
            $privateThread->setModifiedAt(new \DateTime('now'));
            if (array_key_exists('is_doc_reply', $requestParams)) {
                unset($requestParams['is_doc_reply']);
            }
        }

        $this->updateFields($conversation, $requestParams);
        $this->helper->persist($conversation);


        $this->questionImageManager->addConversationImage($conversation, $files);
        $this->helper->persist($privateThread, 'true');

        $isDocReply = false;
        if (Utility::toBool($conversation->getIsDocReply())) {
            $isDocReply = true;
        }

        if (!$isDocReply) {
            $this->sendPrivateNotify('doctor', $privateThread->getDoctorId(), $privateThread->getId(), $privateThread->getSubject());
        } else {
            $this->sendPrivateNotify('patient', $privateThread->getUserInfo()->getPractoAccountId(), $privateThread->getId(), $privateThread->getSubject());
        }

        return $this->createThreadResponse($privateThread, $isDocReply);
    }

    /**
     * @param integer $privateThreadId
     * @param integer $practoAccountId
     *
     * @throws ValidationError
     * @return PrivateThread
     */
    public function load($privateThreadId, $practoAccountId)
    {
        $privateThread = $this->helper->loadById($privateThreadId, ConsultConstants::PRIVATE_THREAD_ENTITY_NAME);
        if (empty($privateThread)) {
            return null;
        }
        if ($practoAccountId != $privateThread->getUserInfo()->getPractoAccountId() && $practoAccountId != $privateThread->getDoctorId()) {
            throw new HttpException(Codes::HTTP_FORBIDDEN, 'You do not have access to view this question');
        }
        if (empty($privateThread)) {
            @$error['error'] = 'No such thread exists';
            throw new ValidationError($error);
        }
        $isDoctor = ($practoAccountId == $privateThread->getUserInfo()->getPractoAccountId()) ? false : true;
        return $this->createThreadResponse($privateThread, $isDoctor);
    }

    /**
     * @param integer $practoAccountId
     * @param boolean $isDoctor
     * @param Request $request
     *
     * @return array PrivateThread
     */
    public function loadAll($practoAccountId, $isDoctor, $request)
    {
        $limit = (array_key_exists('limit', $request)) ? $request['limit'] : 30;
        $offset = (array_key_exists('offset', $request)) ? $request['offset'] : 0;

        /**
         * @var PrivateThreadRepository $er
         */
        $er = $this->helper->getRepository(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME);

        if ($isDoctor) {
            $privateThreads = $er->getDoctorPrivateThreads($practoAccountId, $limit, $offset);
            if (!empty($privateThreads)) {
                $privateThreadsTmp = array();
                foreach ($privateThreads as $privateThread) {
                    $privateThreadTmp = array();
                    $privateThreadTmp['id'] = $privateThread['id'];
                    $privateThreadTmp['subject'] = $privateThread['subject'];
                    $privateThreadTmp['last_modified_time'] = $privateThread['last_modified_time'];
                    $privateThreadTmp['latest_question_text'] = $privateThread['question'];
                    $privateThreadTmp['has_images'] = $privateThread['images_count'] > 0;
                    $userInfo = $this->retrieveUserProfileUtil->retrieveUserProfileNew($privateThread['user_info']);
                    $privateThreadTmp['patient_name'] = $userInfo->getName();
                    $privateThreadTmp['patient_image'] = $userInfo->getProfilePicture();

                    $userInfoList = array('bloodGroup', 'occupation', 'location', 'heightInCms', 'weightInKgs', 'allergies', 'medications', 'prevDiagnosedConditions');
                    $hasAdditionalDetails = false;
                    foreach ($userInfoList as $option) {
                        $getter = 'get'.$option;
                        if (method_exists($userInfo, $getter)) {
                            if (!empty($userInfo->$getter()) && $userInfo->$getter() != 'null') {
                                $hasAdditionalDetails = true;
                                break;
                            }
                        }
                    }
                    $privateThreadTmp['has_additional_details'] = $hasAdditionalDetails;

                    $privateThreadsTmp[] = $privateThreadTmp;
                }
                $privateThreads = $privateThreadsTmp;
            }

        } else {
            $privateThreads = $er->getPatientPrivateThreads($practoAccountId, $limit, $offset);
        }

        if (empty($privateThreads)) {
            return null;
        }

        return array("private_threads" => $privateThreads);
    }

    /**
     * @param PrivateThread $privateThread
     * @return Object DetailedPrivateThread
     */
    private function createThreadResponse($privateThread, $isDoctor)
    {
        $er = $this->helper->getRepository(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME);
        $ecr = $this->helper->getRepository(ConsultConstants::CONVERSATION_ENTITY_NAME);
        $privateThreadResponse = array();
        $privateThreadResponse['id'] = $privateThread->getId();
        $privateThreadResponse['subject'] = $privateThread->getSubject();
        $privateThreadResponse['base_question_id'] = $privateThread->getQuestion()->getId();
        $privateThreadResponse['created_at'] = $privateThread->getCreatedAt();
        $privateThreadResponse['conversation'] = $ecr->findBy(array('privateThread' => $privateThread, 'softDeleted' => 0));
        $userInfo = $privateThread->getUserInfo();
        if (!$userInfo->isIsRelative()) {
            $userInfo = $this->retrieveUserProfileUtil->retrieveUserProfileNew($privateThread->getUserInfo());
        }

        $privateThreadResponse['patient'] = $this->populatePatientInfo($userInfo);
        if (!$isDoctor) {
            $practoAccountId = $privateThread->getUserInfo()->getPractoAccountId();
            $privateThreadResponse['followups_remaining'] = $er->checkFollowUpCount($practoAccountId, $privateThread);
            $privateThreadResponse['doctor'] = $this->populateDoctorInfo($privateThread->getDoctorId());
        }

        return $privateThreadResponse;
    }

    /**
     * @param UserInfo $userInfo
     * @return DetailPatientInfoResponse $patientInfo
     */
    private function populatePatientInfo(UserInfo $userInfo)
    {
        $patientInfo = new DetailPatientInfoResponse();
        $patientInfo->setId($userInfo->getId());
        $patientInfo->setAllergies($userInfo->getAllergies());
        $patientInfo->setMedications($userInfo->getMedications());
        $patientInfo->setPrevDiagnosedConditions($userInfo->getPrevDiagnosedConditions());
        $patientInfo->setHeightInCms($userInfo->getHeightInCms());
        $patientInfo->setWeightInKgs($userInfo->getWeightInKgs());
        $patientInfo->setBloodGroup($userInfo->getBloodGroup());
        $patientInfo->setAge($userInfo->getAge());
        $patientInfo->setGender($userInfo->getGender());
        $patientInfo->setOccupation($userInfo->getOccupation());
        $patientInfo->setLocation($userInfo->getLocation());
        $patientInfo->setName($userInfo->getName());
        $patientInfo->setAllergyStatus($userInfo->getAllergyStatus());
        $patientInfo->setPrevDiagnosedConditionsStatus($userInfo->getDiagnosedConditionStatus());
        $patientInfo->setMedicationStatus($userInfo->getMedicationStatus());
        $patientInfo->setProfilePicture($userInfo->getProfilePicture());

        return $patientInfo;
    }

    /**
     * @param $practoAccountId
     *
     * @return \ConsultBundle\Entity\DoctorEntity|null
     */
    private function populateDoctorInfo($practoAccountId)
    {
        $doctor = $this->doctorManager->getConsultSettingsByPractoAccountId($practoAccountId);

        return DoctorEntity::getEntityFromConsultSettings($doctor);
    }

    /**
     * @param BaseEntity $entity
     * @param array      $requestParams - data for the updation
     * @throws ValidationError
     */
    private function updateFields($entity, $requestParams)
    {
        $entity->setAttributes($requestParams);

        try {
            $this->validator->validate($entity);
        } catch (ValidationError $e) {
            throw new ValidationError($e->getMessage());
        }

        return;
    }

    /**
     * Send private question assigned notify
     *
     * @param string $to          - Sending it to
     * @param string $toAccountId - Sending it to account id
     * @param string $questionId  - Question id
     * @param string $subject
     */
    private function sendPrivateNotify($to, $toAccountId, $questionId, $subject)
    {
        if ($to == 'doctor') {
            $this->queue->setQueueName(Queue::CONSULT_GCM)
                ->sendMessage(
                    json_encode(
                        array(
                        "type"=>"consult",
                        "message"=>array('text'=>"A Private Question has been assigned to you.", 'question_id'=>$questionId, 'is_private'=>true, 'subject'=>$subject, 'consult_type'=>ConsultConstants::PRIVATE_THREAD_NOTIFICATION_TYPE ),
                        "send_to"=>"synapse",
                        "account_ids"=>array($toAccountId),
                        )
                    )
                );
        } elseif ($to == 'patient') {
            $this->queue->setQueueName(Queue::CONSULT_GCM)
                ->sendMessage(
                    json_encode(
                        array(
                        "type"=>"consult",
                        "message"=>array('text'=>"Your Private Query has been answered.", 'question_id'=>$questionId, 'is_private'=>true, 'subject'=>$subject, 'consult_type'=>ConsultConstants::PRIVATE_THREAD_NOTIFICATION_TYPE),
                        "send_to"=>"fabric",
                        "account_ids"=>array($toAccountId),
                        )
                    )
                );
        }
    }
}
