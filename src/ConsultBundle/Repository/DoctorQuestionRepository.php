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

}