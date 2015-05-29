<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 15:18
 */

namespace ConsultBundle\Helper;

use ConsultBundle\Utility\CacheUtils;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use ConsultBundle\Validator\Validator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Util\Codes;
use GuzzleHttp\Message\Response;

class Helper
{

    /**
     * @var EntityManager
     */
    protected  $entityManager;
    protected $cacheUtils;

    public function __construct(Doctrine $doctrine, CacheUtils $cacheUtils)
    {
      $this->entityManager = $doctrine->getManager();
      $this->cacheUtils = $cacheUtils;

    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * LoadAll
     *
     * @param  $entityName
     *
     * @return entity
     */
    public function loadAll($entityName)
    {
        //$entity = $this->entityManager->getRepository($entityName)->findAll();
        $entity = $this->entityManager->getRepository($entityName)->findBy(array('softDeleted' => 0));


        if (is_null($entity)) {
            return null;
        }

        return $entity;
    }

    /**
     * @param $id
     * @param $entityName
     *
     * @return mixed
     */
    public function  loadById($id, $entityName)
    {

        $entity = $this->entityManager->getRepository($entityName)->find($id);


        if (is_null($entity)) {
            return null;
        }

        return $entity;
    }

    /**
     * @param $entityName
     * @return EntityRepository|null
     */

    public function getRepository($entityName)
    {

        $entityRepository = $this->entityManager->getRepository($entityName);

        if(is_null($entityRepository))
        {
          return null;
        }


        return $entityRepository;
    }


    public function remove($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }



    /**
     * @param $entity
     * @param $params
     * @return mixed
     */
    public function update($entity, $params)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param $entity
     * @param $flush
     */
    public function persist($entity, $flush=null)
    {
        if($entity != null){
            $this->entityManager->persist($entity);
        }

        if($flush)
        {
            $this->entityManager->flush();
        }
    }

    /**
     * @param $fields
     * @param array $data
     * @throws \HttpException
     */
    public function checkForMandatoryFields($fields, array $data )
    {
        $errors = new ArrayCollection();
        //var_dump($data);
        foreach($fields as $field)
        {
            //var_dump($field);
            if(!array_key_exists($field, $data))
            {

                 $errors->add($field . " is Mandatory");
            }
        }

        if($errors->count()>0)
        {
            throw new \HttpException(json_encode($errors->getValues()), Codes::HTTP_BAD_REQUEST);
        }
    }

    protected function getFromCache($entityId)
    {
        // TODO: Implement getFromCache() method.
    }

    protected function updateCache($entity)
    {
        // TODO: Implement updateCache() method.
    }
}
