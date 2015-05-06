<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 06/05/15
 * Time: 12:05
 */

namespace ConsultBundle\Repository;


use Doctrine\ORM\EntityRepository;

class DoctorQuestionRepository extends EntityRepository{


    /**
     * @param $doctorId
     * @param $state
     * @return array
     */
    public function findDoctorQuestionsForState($doctorId, $state)
    {
        $qb = $this->_em->createQueryBuilder();
        $questions = null;

        try{
             $qb->select(array('q'))
                ->from("ConsultBundle:Question", 'q')
                ->innerJoin('q.doctorQuestions', 'dq')

                ->where($qb->expr()->andX(
                    $qb->expr()->eq('dq.practoAccountId', ':doctorId'),
                    $qb->expr()->eq('dq.state', ':state')
                ))->setParameter('state', $state)
                 ->setParameter('doctorId', $doctorId)
               ->addOrderBy('dq.modifiedAt', 'DESC');

            $questions = $qb->getQuery()->getArrayResult();
        }catch(\Exception $e)
        {
            //return $qb->getQuery();
            return $e->getMessage();
        }

        return $questions;

    }

    /**
     * @param $doctorId
     * @param $state
     * @return array
     */
    public function findDoctorQuestionsForAState($doctorId, $state, $maxResults=null)
    {
        $query = $this->_em->createQuery("SELECT q FROM ConsultBundle\Entity\Question q join q.doctorQuestions dq WHERE dq.practoAccountId = :doctorId AND dq.state= :state AND q.softDeleted = 0 AND dq.softDeleted= 0");
        $query->setParameter('doctorId', $doctorId);
        $query->setParameter('state', $state);
        if($maxResults!= null)
        $query->setMaxResults($maxResults);

        $questions = $query->getResult();
       /* foreach( $questions as $question)
            {
                $question->getDoctorQuestions();
            }*/
        return $questions;


    }

}