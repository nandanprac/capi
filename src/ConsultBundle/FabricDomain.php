<?php

namespace ConsultBundle;

use Symfony\Component\HttpFoundation\Request;

/**
 * Fabric Domain
 */
class FabricDomain
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
     *
     * @param string $subdomain - Subdomain
     *
     * @return string
     */
    public function getHost($subdomain = null)
    {
        $fabricHost = $this->request->getSchemeAndHttpHost();
        if (!$subdomain) {
            return $fabricHost;
        }
        $origSubdomain = 'www';

        return str_replace($origSubdomain, $subdomain, $fabricHost);
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
