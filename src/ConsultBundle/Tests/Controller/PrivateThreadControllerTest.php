<?php

/**
  * Functional test for all private question APIs
  */
namespace ConsultBundle\Tests\Controller;

use ConsultBundle\Utility\AuthenticationUtils;
use ConsultBundle\EventListener\SecurityListener;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use FOS\RestBundle\Util\Codes;

/**
  * This class has testcases for all private question related APIs
  */
class PrivateThreadControllerTest extends WebTestCase
{
    private $pdo = null;
    private $conn = null;

    /**
     * Setup the class
     *
     * @return null
     */
    public static function setUpBeforeClass()
    {
        print "==== Starting PrivateThreadControllerTest ====";
    }

    /**
     * Setup
     *
     * @return null
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $practoAccountId = 1;
        $profileToken = 'junk_value';

        $authenticationStub = $this->getMockBuilder('ConsultBundle\Utility\AuthenticationUtils')->setMethods(['authenticateWithAccounts'])->disableOriginalConstructor()->getMock();
        $authenticationStub->expects($this->any())->method('authenticateWithAccounts')->will(
            $this->returnCallback(
                function ($practoAccountId, $profileToken) {
                    return true;
                }
            )
        );
        $this->client->getContainer()->set('consult.account_authenticator_util', $authenticationStub);

        $securityStub = $this->getMockBuilder('ConsultBundle\EventListener\SecurityListener')->disableOriginalConstructor()->getMock();
        #$securityStub->expects($this->any())->method('onKernelRequest')->willReturn(true);
        $securityStub->expects($this->any())->method('onKernelRequest')->will(
            $this->returnCallback(
                function () {
                    $_SESSION['validated'] = true;
                    $_SESSION['authenticated_user']['id'] = 1;
                }
            )
            );
        $this->client->getContainer()->set('listener.security_listener', $securityStub);

    }

    /**
     * Teardown
     *
     * @return null
     */
    public static function tearDownAfterClass()
    {
    }

    /**
     * Test for access forbidden
     *
     * @return null
     */
    public function testPostPrivateThread()
    {
        $crawler = $this->client->request(
            'POST',
            '/privates/threads'
        );
        $response = $this->client->getResponse();
        $this->assertEquals(
            Codes::HTTP_FORBIDDEN,
            $response->getStatusCode(),
            "Expected error - Access forbidden was not recieved"
        );
    }

}
