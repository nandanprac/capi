<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Utils\Utility;

/**
 * HttpManager
 */
class HttpManager
{
    private $buzz;
    private $client;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client = new \Buzz\Client\Curl();
        $this->client->setTimeout(20);
        $this->buzz = new \Buzz\Browser($this->client);
    }

    /**
     * make appropriate response
     *
     * @param array  $response    - Response
     * @param string $contentType - Content Type
     *
     * @return array
     */
    private function makeResponse($response, $contentType)
    {
        if ($contentType == 'application/json') {
            return array(
                'status_code' => $response->getStatusCode(),
                'content'     => json_decode($response->getContent(), true)
            );
        } else {
            $content = json_decode($response->getContent(), true);

            return array(
                'status_code' => $response->getStatusCode(),
                'content'     => ($content ? : $response->getContent())
            );
        }
    }

    /**
     * return exception.
     *
     * @param Exception $ex - exception
     *
     * @return array
     */
    private function makeExceptionResponse($ex)
    {
        return array(
            'status_code' => 500,
            'content'     => $ex->getMessage()
            );
    }

    /**
     * Add Basic Auth Listener
     *
     * @param string $username - Username for basic auth
     * @param array  $password - Password for basic auth
     *
     * @return array
     */
    public function addBasicAuthListener($username, $password)
    {
        $this->buzz->addListener(new \Buzz\Listener\BasicAuthListener($username, $password));
    }

    /**
     * Make Http Get call
     *
     * @param string $url         - Resource
     * @param array  $headers     - Http Headers
     * @param array  $params      - Get Parameters
     * @param string $contentType - Content Type
     *
     * @return array
     */
    public function get($url, array $headers = array(), array $params = array(), $contentType = 'application/json')
    {
        try {
            if (count($params) > 0) {
                $url = $url.'?'.Utility::buildQuery($params);
            }
            $response = $this->buzz->get($url, $headers, $params);

            return $this->makeResponse($response, $contentType);
        } catch (\HttpException $ex) {
            return $this->makeExceptionResponse($ex);
        }
    }

    /**
     * Making a post request using buzz.
     *
     * @param string $url         - Resource
     * @param array  $headers     - Http Headers
     * @param array  $params      - Get Parameters
     * @param string $contentType - Content Type
     *
     * @return array
     */
    public function post($url, array $headers = array(), $params = array(), $contentType = 'application/json')
    {
        try {
            if ($contentType == 'application/x-www-form-urlencoded') {
                $payload = Utility::buildQuery($params);
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
                $accept = '*/*';
            } else {
                $payload = json_encode($params);
                $headers['Content-Type'] = 'application/json';
                $accept = 'application/json';
            }
            $response = $this->buzz->post($url, $headers, $payload, $accept);

            return $this->makeResponse($response, $accept);
        } catch (\HttpException $ex) {
            return $this->makeExceptionResponse($ex);
        }
    }

    /**
     * Making a patch request using buzz.
     *
     * @param string $url         - Resource
     * @param array  $headers     - Http Headers
     * @param array  $params      - Body
     * @param string $contentType - Content Type
     *
     * @return array
     */
    public function patch($url, array $headers = array(), $params = array(), $contentType = 'application/json')
    {
        try {
            $response = $this->buzz->patch($url, $headers, $params, $contentType);

            return $this->makeResponse($response, $contentType);
        } catch (\HttpException $ex) {
            return $this->makeExceptionResponse($ex);
        }
    }

    /**
     * delete http request using buzz.
     *
     * @param string $url         - Resource
     * @param array  $headers     - Http Headers
     * @param array  $params      - Body
     * @param string $contentType - Content Type
     *
     * @return array
     */
    public function delete($url, array $headers = array(), $params = array(), $contentType = 'application/json')
    {
        try {
            $response = $this->buzz->delete($url, $headers, $params, $contentType);

            return $this->makeResponse($response, $contentType);
        } catch (\HttpException $ex) {
            return $this->makeExceptionResponse($ex);
        }
    }
}
