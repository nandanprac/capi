<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 15:18
 */

namespace ConsultBundle\Helper;

//use ConsultBundle\Utility\CacheUtils;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Util\Codes;
use ConsultBundle\Entity\BaseEntity;
use Symfony\Bridge\Monolog\Logger;

/**
 * Class Helper
 *
 * @package ConsultBundle\Helper
 */
class Helper
{

    /**
     * @var EntityManager
     */
    protected $entityManager;
    protected $cacheUtils;

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param \Symfony\Bridge\Monolog\Logger           $logger
     */
    public function __construct(Doctrine $doctrine, Logger $logger)
    {
        $this->entityManager = $doctrine->getManager();

        //$loggerSymfony = $this->get('logger');

        $dLogger = new \Doctrine\DBAL\Logging\DebugStack();

        $doctrine ->getConnection()
            ->getConfiguration()
            ->setSQLLogger($dLogger);

        $logger->info(json_encode($dLogger->queries));

        //$loggerSymfony->debug($logger->queries);

        //$this->cacheUtils = $cacheUtils;

    }

    /**
     * @param String $entityName
     *
     * @return array|null
     */
    public function loadAll($entityName)
    {

        $entity = $this->entityManager->getRepository($entityName)->findBy(array('softDeleted' => 0));


        if (empty($entity)) {
            return null;
        }

        return $entity;
    }

    /**
     * @param int    $id
     * @param string $entityName
     *
     * @return mixed
     */
    public function loadById($id, $entityName)
    {

        $entity = $this->entityManager->getRepository($entityName)->find($id);


        if (empty($entity)) {
            return null;
        }

        return $entity;
    }

    /**
     * @param string $entityName
     * @return EntityRepository|null
     */
    public function getRepository($entityName)
    {

        $entityRepository = $this->entityManager->getRepository($entityName);

        if (is_null($entityRepository)) {
            return null;
        }


        return $entityRepository;
    }


    /**
     * @param BaseEntity $entity
     */
    public function remove($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }





    /**
     * @param BaseEntity $entity
     * @param boolean    $flush
     */
    public function persist($entity, $flush = null)
    {
        if ($entity != null) {
            $this->entityManager->persist($entity);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param array $fields
     * @param array $data
     * @throws \HttpException
     */
    public function checkForMandatoryFields($fields, array $data)
    {
        $errors = new ArrayCollection();
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                 $errors->add($field." is Mandatory");
            }
        }

        if ($errors->count() > 0) {
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
