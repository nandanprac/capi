<?php

namespace ConsultBundle\Tests\Controller;

use ConsultBundle\Utility\AuthenticationUtils;
use ConsultBundle\EventListener\SecurityListener;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * Base Web Test Case Class
 */
class BaseTestCase extends WebTestCase
{
    protected static $application;
    protected $client;

    /**
     * Setting up before each test case
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $authenticateStub = $this->getMockBuilder('ConsultBundle\EventListener\SecurityListener')->disableOriginalConstructor()->getMock();

        $authenticateStub->expects($this->any())->method('onKernelRequest')->will(
            $this->returnCallback(
                function ($event) {
                    $_SESSION['validated'] = true;
                    $_SESSION['authenticated_user']['id'] = 0;
                }
            )
        );
        $this->client->getContainer()->set('listener.security_listener', $authenticateStub);
    }

    /**
     * @param string $uri   - url to do the post request on
     * @param array  $data  - post data
     * @param array  $param - queryparam for post request id any
     *
     * @return array
     */
    public function post($uri, array $data, $param = array())
    {
        $headers = array('Content-Type' => 'application/json');
        $content = json_encode($data);
        $this->client->request('POST', $uri, $param, array(), $headers, $content);

        return $this->client->getResponse();
    }

    /**
     * @param string $uri   - url to do the post request on
     * @param array  $param - queryparam for post request id any
     *
     * @return array
     */
    public function get($uri, array $param = array())
    {
        $headers = array('Content-Type' => 'application/json', 'X-Profile-Token' => 'xxxxxx');
        $this->client->request('GET', $uri, $param, array(), $headers);

        return $this->client->getResponse();
    }


    /**
     * @param string $command - command to be executed
     *
     * @return array
     */
    public static function runCommand($command)
    {
        $command = sprintf('%s  --env=test', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    /**
     * @return application
     */
    public static function getApplication()
    {
        $client = static::createClient();
        if (null === self::$application) {
            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }
}
