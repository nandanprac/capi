<?php

namespace ConsultBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use ConsultBundle\Entity\Speciality;
use ConsultBundle\Entity\SubSpeciality;

/**
 * Loads Speciality and SubSpeciality
 */
class LoadSpecialityData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * Setter for ContainerInterface
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Loads and runs fixture
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $em = $this->container->get('doctrine')->getManager();
        $path = $this->container->get('kernel')->getRootDir().'/../fixtures/specialties.csv';
        $fp = fopen($path, 'r');
        while ($row = fgetcsv($fp, 1024, ',')) {
            $speciality = new Speciality();
            $speciality->setName($row[0]);
            $speciality->setDescription($row[0]);
            $em->persist($speciality);

            for ($i=1; $i < count($row); $i++) {
                $subspeciality = new SubSpeciality();
                $subspeciality->setSpeciality($speciality);
                $subspeciality->setSubSpeciality($row[$i]);
                $em->persist($subspeciality);
            }

        }
        $em->flush();
    }
}
