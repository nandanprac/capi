<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 16/07/15
 * Time: 17:16
 */

namespace ConsultBundle\Tests\Manager;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class DoctorManagerTest
 *
 * @package ConsultBundle\Tests\Manager
 */
class DoctorManagerTest extends KernelTestCase
{
    private static $doctorManager;

    public  function setUp()
    {
        self::bootKernel(array('dev', true));


        //get the DI container
        self::$doctorManager = static::$kernel->getContainer()->get('consult.doctor_manager');

    }
    /**
     * something
     */
    public function testGetAppropriateDoctors()
    {
        $result = self::$doctorManager->getAppropriateDoctors('', 'Orthopedist');
        $this->assertEquals(2, count($result));

    }

    /**
     * dentist check
     */
    public function testGetAppropriateDoctorsDentist()
    {
        $result = self::$doctorManager->getAppropriateDoctors('', 'Dentist');
        $this->assertEquals(3, count($result));

    }

    public function testImageThumbnail()
    {
        var_dump("123 Inside Image Thumbnail");
        $thumb = new \Imagick('/home/vagrant/www/consult-api/app/myimage.jpg');
        $thumb->scaleImage(200,200, 1);
        $thumb->writeImage('/home/vagrant/www/consult-api/app/mythumb.jpg');
        $thumb->clear();
        $thumb->destroy();
    }
}