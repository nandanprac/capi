<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 12:42
 */

namespace ConsultBundle\Controller;


use ConsultBundle\Entity\DoctorQuestion;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;
use Doctrine\Common\Persistence\ObjectRepository;

class DoctorQuestionsController extends FOSRestController
{
    /**
     * @param Request $request
     * @return DoctorQuestion
     *
     * @View()
     */
   public function postDoctorQuestionsAction(Request $request)
   {
      $questionId = $request->request->get('questionId');
       $doctorsId = $request->request->get('doctorsId');

       $er=$this->getDoctrine()->getManager()->getRepository("ConsultBundle:Question");
       $question = $er->find($questionId);

       $doctorsQuestion = new DoctorQuestion();
       $doctorsQuestion->setPractoAccountId($doctorsId);
       $doctorsQuestion->setQuestion($question);

       $em = $this->getDoctrine()->getManager();
       $em->persist($doctorsQuestion);
       $em->flush();

       return $doctorsQuestion;
      // $doctorQuestionManager = $this->get('consult.doctorQuestionManager');
      // $doctorQuestionManager->
   }


}