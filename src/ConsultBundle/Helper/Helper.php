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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

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

    /**
     * LoadAll
     *
     * @param  $entityName
     *
     * @return entity
     */
    public function loadAll($entityName)
    {
        $entity = $this->entityManager->getRepository($entityName)->findAll();


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
     * Get Repository
     *
     * @param  $entityName
     *
     * @return entity
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

        if($flush != null)
        {
            $this->entityManager->flush();
        }

        return $entity;
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
