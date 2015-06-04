<?php

/**
  * Functional test for all question APIs
  */
namespace ConsultBundle\Tests\Controller;

use ConsultBundle\Utility\AuthenticationUtils;
use ConsultBundle\EventListener\SecurityListener;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use FOS\RestBundle\Util\Codes;

/**
  * This class has testcases for all question related APIs
  */
class QuestionsControllerTest extends WebTestCase
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
        print "==== Starting QuestionsControllerTest ====";
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
        $securityStub->expects($this->any())->method('onKernelRequest')->willReturn(true);
        //$securityStub->expects($this->once())->method('onKernelRequest')->will($this->returnValue($authenticationStub));
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
    public function testPostQuestionAccessForbidden()
    {
        $client1 = static::createClient();
        $crawler = $client1->request(
            'POST',
            '/questions'
        );
        $response = $client1->getResponse();
        $this->assertEquals(
            Codes::HTTP_FORBIDDEN,
            $response->getStatusCode(),
            "Expected error - Access forbidden was not recieved"
        );
    }

    /**
     * Test for bad request error when question text is blank
     *
     * @return null
     */
    public function testPostQuestionBadRequest()
    {
        $crawler = $this->client->request(
            'POST',
            '/questions',
            array('practo_account_id' => '1'),
            array(),
            array('X-PROFILE-TOKEN' => 'junk_value')
        );
        $response = $this->client->getResponse();
        $this->assertEquals(
            Codes::HTTP_BAD_REQUEST,
            $response->getStatusCode(),
            "Expected error - Bad request was not recieved"
        );
        $this->assertContains(
            "This value should not be blank",
            $response->getContent(),
            "Bad request error did not show up"
        );
    }

    /**
     * Test for a basic question post flow
     *
     * @return null
     */
    public function testPostQuestionBasicPositive()
    {
        //$client = static::createClient();
        $crawler = $this->client->request(
            'POST',
            '/questions',
            array('question' => '{"practo_account_id":"1","text":"test question"}'),
            array(),
            array()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(
            Codes::HTTP_CREATED,
            $response->getStatusCode(),
            "Success is expected here"
        );
        $this->assertContains(
            "id",
            $response->getContent(),
            "A valid entry did not get created"
        );
    }


    /**
     * Test for post question with additional details
     *
     * @return null
     */
    public function testPostQuestionDetailsYourselfPositive()
    {
        //$client = static::createClient();
        $crawler = $this->client->request(
            'POST',
            '/questions',
            array('question' => '{"practo_account_id":"1",
                                  "text":"test question",
                                  "additional_info": {"allergies":"abcd"},
                                  "user_profile_details": {"is_some_else":false}}', ),
            array(),
            array()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(
            Codes::HTTP_CREATED,
            $response->getStatusCode(),
            "Success is expected here"
        );
        $this->assertContains(
            "id",
            $response->getContent(),
            "A valid entry did not get created"
        );
    }

    /**
     * Test for post question for someone else
     *
     * @return null
     */
    public function testPostQuestionDetailsSomeoneElsePositive()
    {
        //$client = static::createClient();
        $crawler = $this->client->request(
            'POST',
            '/questions',
            array('question' => '{"practo_account_id":"1","text":"test question",
                                  "additional_info":{"medications":"abcd"},
                                  "user_profile_details":{"is_some_else":true,
                                                          "gender":"M"}}', ),
            array(),
            array()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(
            Codes::HTTP_CREATED,
            $response->getStatusCode(),
            "Success is expected here"
        );
        $this->assertContains(
            "id",
            $response->getContent(),
            "A valid entry did not get created"
        );
    }

    /**
     * Test for get a particular question
     *
     * @return null
     */
    public function testGetQuestionById()
    {
        //$client = static::createClient();
        $crawler = $this->client->request(
            'GET',
            '/questions/1'
        );
        $response = $this->client->getResponse();
        $this->assertEquals(
            Codes::HTTP_OK,
            $response->getStatusCode(),
            "Success is expected here"
        );
        $this->assertContains(
            "id",
            $response->getContent(),
            "A valid entry was not returned"
        );
    }

    /**
     * Test for get a list of questions
     *
     * @return null
     */
    public function testGetAllQuestions()
    {
        //$client = static::createClient();
        $crawler = $this->client->request(
            'GET',
            '/questions'
        );
        $response = $this->client->getResponse();
        //        echo $response->getContent();
        $this->assertEquals(
            Codes::HTTP_OK,
            $response->getStatusCode(),
            "Success is expected here"
        );
        $this->assertContains(
            "id",
            $response->getContent(),
            "Valid response was not sent"
        );
    }
}
