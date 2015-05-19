<?php

namespace ConsultBundle;

use Symfony\Component\HttpFoundation\Request;

/**
 * Consult Domain
 */
class ConsultDomain
{
    protected $request;

    /**
     * Constructor
     * @param Request $request - Request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get Host
     * @param string $subdomain - Subdomain
     *
     * @return string
     */
    public function getHost($subdomain=null)
    {
        $consultHost = $this->request->getSchemeAndHttpHost();
        if (!$subdomain) {
            return $consultHost;
        }
        $origSubdomain = 'www';

        return str_replace($origSubdomain, $subdomain, $consultHost);
    }

    /**
     * Get Current Url.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->request->getUri();
    }
}
