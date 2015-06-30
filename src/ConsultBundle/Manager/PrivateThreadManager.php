<?php

namespace ConsultBundle\Manager;

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

    /**
     * @param UserManager               $userManager
     * @param Queue                     $queue
     * @param RetrieveUserProfileUtil   $retrieveUserProfileUtil
     * @param RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil
     * @param DoctorManager             $doctorManager
     */
    public function __construct(
        UserManager $userManager,
        Queue $queue,
        RetrieveUserProfileUtil $retrieveUserProfileUtil,
        RetrieveDoctorProfileUtil $retrieveDoctorProfileUtil,
        DoctorManager $doctorManager
    ) {
        $this->userManager = $userManager;
        $this->queue = $queue;
        $this->retrieveUserProfileUtil = $retrieveUserProfileUtil;
        $this->retrieveDoctorProfileUtil = $retrieveDoctorProfileUtil;
        $this->doctorManager = $doctorManager;
    }

    /**
     * @param Entity   $entity
     * @param array    $requestParams - data for the updation
     * @throws ValidationError
     */
    public function updateFields($entity, $requestParams)
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
     * @param array   $requestParams   - parameters passed for creating new question object
     * @param integer $practoAccountId - practo account id
     * @param string  $profileToken    - profile token of the user
     * @return \ConsultBundle\Entity\PrivateThread
     * @throws \ConsultBundle\Manager\ValidationError
     */
    public function add($requestParams, $practoAccountId, $profileToken = null)
    {
        $er = $this->helper->getRepository(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME);
        $privateThreadId = array_key_exists('private_thread_id', $requestParams) ? $requestParams['private_thread_id'] : null;

        if (!empty($privateThreadId)) {
            $privateThread = $this->helper->loadById($privateThreadId, ConsultConstants::PRIVATE_THREAD_ENTITY_NAME);
            if (empty($privateThread)) {
                @$error['error'] = 'Private thread with the provided id could not be found';
                throw new ValidationError($error);
            }

            if (array_key_exists('is_doc_reply', $requestParams) and Utility::toBool($requestParams['is_doc_reply'])) {
                if ($privateThread->getDoctorId() != $practoAccountId) {
                    throw new HttpException(Codes::HTTP_FORBIDDEN, 'You are not allowed to answer this question');
                }
            } else {
                if ($privateThread->getUserInfo()->getPractoAccountId() != $practoAccountId) {
                    throw new HttpException(Codes::HTTP_FORBIDDEN, 'You are not allowed to ask any question here');
                }
                if ($er->checkFollowUpCount($practoAccountId, $privateThread) <= 0) {
                    @$error['error'] = 'You have exhausted your follow up questions limit';
                    throw new ValidationError($error);
                }
                $lastConversation = $this->helper->getRepository(ConsultConstants::CONVERSATION_ENTITY_NAME)
                                    ->findBy(array(), array('createdAt' => 'DESC'), 1, 0);
                if (!empty($lastConversation)) {
                    if (!Utility::toBool($lastConversation[0]->getIsDocReply())) {
                        @$error['error'] = 'Wait for doctor\'s reply before following up again';
                        throw new ValidationError($error);
                    }
                }
            }

            $conversation = new Conversation();
            $conversation->setPrivateThread($privateThread);
            $privateThread->setModifiedAt(new \DateTime('now'));

        } else {
            if (!array_key_exists('reply_id', $requestParams)) {
                @$error['error'] = 'Either id of previous reply or id of the private thread is required';
                throw new ValidationError($error);
            }
            if ($er->getPatientPrivateThreads($practoAccountId)) {
                @$error['error'] = 'You have already started a private thread. Cannot start another';
                throw new ValidationError($error);
            }

            $reply_id = $requestParams['reply_id'];
            $reply = $this->helper->loadById($reply_id, ConsultConstants::DOCTOR_REPLY_ENTITY_NAME);
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
            $subject = array_key_exists('subject', $requestParams) ? $requestParams['subject'] : $reply->getDoctorQuestion()->getQuestion()->getSubject();
            $privateThread->setSubject($subject);
            
            $conversation = new Conversation();
            $conversation->setPrivateThread($privateThread);
            $privateThread->setModifiedAt(new \DateTime('now'));
        }

        $this->updateFields($conversation, $requestParams);
        $this->helper->persist($privateThread, 'true');
        $this->helper->persist($conversation, 'true');

        $isDocReply = false;
        if (Utility::toBool($conversation->getIsDocReply())) {
            $isDocReply = true;
        }

        return $this->createThreadResponse($privateThread, $isDocReply);
    }

    /**
     * @param integer $privateThreadId
     * @param integer $practoAccountId
     *
     * @throws Httpexception
     * @return PrivateThread
     */
    public function load($privateThreadId, $practoAccountId)
    {
        $privateThread = $this->helper->loadById($privateThreadId, ConsultConstants::PRIVATE_THREAD_ENTITY_NAME);
        if ($practoAccountId != $privateThread->getUserInfo()->getPractoAccountId() and $practoAccountId != $privateThread->getDoctorId()) {
            throw new HttpException(Codes::HTTP_FORBIDDEN, 'You do not have access to view this question');
        }
        if (empty($privateThread)) {
            @$error['error'] = 'No such thread exists';
            throw new ValidationError($error);
        }
        $practoAccountId = ($practoAccountId == $privateThread->getUserInfo()->getPractoAccountId()) ? false : true;
        return $this->createThreadResponse($privateThread, $practoAccountId);
    }

    /**
     * @param integer $practoAccountId
     *
     * @return array PrivateThread
     */
    public function loadAll($practoAccountId, $is_doctor)
    {
        $er = $this->helper->getRepository(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME);
        if ($is_doctor) {
            $privateThreads = $er->getDoctorPrivateThreads($practoAccountId);
        } else {
            $privateThreads = $er->getPatientPrivateThreads($practoAccountId);
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
    private function createThreadResponse($privateThread, $is_doctor)
    {
        $er = $this->helper->getRepository(ConsultConstants::PRIVATE_THREAD_ENTITY_NAME);
        $privateThreadResponse = array();
        $privateThreadResponse['subject'] = $privateThread->getSubject();
        $privateThreadResponse['base_question_id'] = $privateThread->getQuestion()->getId();
        $privateThreadResponse['conversation'] = $er->getAllConversationsForThread($privateThread);
        $userInfo = $this->retrieveUserProfileUtil->retrieveUserProfileNew($privateThread->getUserInfo());
        $privateThreadResponse['patient'] = $this->populatePatientInfo($userInfo);
        if (!$is_doctor) {
            $practoAccountId = $privateThread->getUserInfo()->getPractoAccountId();
            $privateThreadResponse['followups_remaining'] = $er->checkFollowUpCount($practoAccountId, $privateThread);
            $privateThreadResponse['doctor_name'] = $this->doctorManager->getConsultSettingsByPractoAccountId($privateThread->getDoctorId())->getName();
        }

        return $privateThreadResponse;
    }

    private function populatePatientInfo(UserInfo $userInfo)
    {
        $patientInfo = new DetailPatientInfoResponse();
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

        return $patientInfo;
    }

}
