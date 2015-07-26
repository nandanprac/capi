<?php

/**
  * Base class for Functional test
  */
namespace ConsultBundle\Tests\Controller;

use ConsultBundle\Utility\AuthenticationUtils;
use ConsultBundle\EventListener\SecurityListener;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
  * This class has setup code required for all functional tests
  */
class BaseTestController extends WebTestCase
{
    private $pdo = null;
    private $conn = null;

    /**
     * Setup the class
     */
    public static function setUpBeforeClass()
    {
        print "\n==== Starting ".get_called_class()." ====\n";
    }

    /**
     * Teardown after all testcases execution
     */
    public static function tearDownAfterClass()
    {
        print "\n==== Ending ".get_called_class()." ====\n";
    }

}
