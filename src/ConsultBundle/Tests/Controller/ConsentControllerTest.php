<?php

/**
  * Functional test for user consent APIS
  */
namespace ConsultBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
  * This class has testcases for all question related APIs
  */
class ConsentControllerTest extends BaseTestController
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->mocker();
    }

    /**
     * mocks the security listener
     */
    public function mocker()
    {
        $this->client = static::createClient();
        $profileToken = 'junk_value';

        $securityStub = $this->getMockBuilder('ConsultBundle\EventListener\SecurityListener')->setMethods(['onKernelRequest'])->disableOriginalConstructor()->getMock($cloneArguments = FALSE);
        $securityStub->expects($this->any())->method('onKernelRequest')->will(
            $this->returnCallback(
                function ($event, $str) {
                    $_SESSION['validated'] = true;
                    $_SESSION['authenticated_user']['id'] = 10;
                    return true;
                }
            )
        );
        $this->client->getContainer()->set('listener.security_listener', $securityStub);
    }

    /**
     * get consent
     */
    public function testGetConsultConsentAPI()
    {
        $crawler = $this->client->request(
            'GET',
            '/user/consent?practo_account_id=10',
            array(),
            array(),
            array('X-PROFILE-TOKEN' => 'junk_value')
        );
        $response = $this->client->getResponse();
        $this->assertContains(
            "false",
            $response->getContent(),
            "Consent is true - this cannot happen"
        );
    }

    /**
     * set consent
     */
    public function testSetConsultConsentAPI()
    {
        $crawler = $this->client->request(
            'POST',
            '/users/consents',
            array('practo_account_id' => 10),
            array(),
            array('X-PROFILE-TOKEN' => 'junk_value')
        );
        $response = $this->client->getResponse();
        $this->assertContains(
            "true",
            $response->getContent(),
            "something is wrong - you just consented!"
        );
    }
}
