<?php

namespace ConsultBundle\Tests\Controller;

use ConsultBundle\Tests\Controller\BaseTestCase;
use FOS\RestBundle\Util\Codes;

/**
 * Notification Controller test cases
 */
class NotificationControllerTest extends BaseTestCase
{
    /**
     * Setup before class
     */
    public static function setUpBeforeClass()
    {
        fwrite(STDOUT, __METHOD__." : Creating Database\n");
        self::runCommand('doctrine:database:create --quiet');
        self::runCommand('doctrine:schema:create --quiet');
    }

    /**
     * Checking get doctor notification when no practo_account_id is given
    public function testGetDoctorNotificationWithoutPractoAccountId()
    {
        var_dump(2);
        $response = $this->get('/doctor/notification', array());
        var_dump($response->getStatusCode());
        $this->assertEquals(Codes::HTTP_FORBIDDEN, $response->getStatusCode());
        var_dump(2);
    }
    */

    /**
     * Check get doctor notification when practo_account_id is given
     */
    public function testGetDoctorNotificationWithPractoAccountId()
    {
        $response = $this->get('/doctor/notification', array('practo_account_id'=>1));
        $this->assertSame(Codes::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Check get doctor notification when practo_account_id is given
     */
    public function testGetUserNotificationWithPractoAccountId()
    {
        $response = $this->get('/user/notification', array('practo_account_id'=>1));
        $this->assertSame(Codes::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Tear down after class
     */
    public static function tearDownAfterClass()
    {
        fwrite(STDOUT, __METHOD__." : Destroying Database\n");
        self::runCommand('doctrine:database:drop --force --quiet');
    }
}
