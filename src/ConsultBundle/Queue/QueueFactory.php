<?php

namespace ConsultBundle\Queue;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Legacy Queue Factory
 */
class QueueFactory extends ContainerAware
{
    /**
     * Get Queue Object
     *
     * @param string $uri
     *
     * @return AbstractQueue
     */
    public function get($uri=null)
    {
        if (!$uri) {
            $uri = $this->container->getParameter('consult_queue.uri');
        }
        $parts = parse_url($uri);
        if (($parts['scheme'] == 'https' || $parts['scheme'] == 'http') &&
            ($hParts = explode('.', $parts['host'], 3)) &&
            ($hParts[0] == 'sqs' && $hParts[2] == 'amazonaws.com')) {
            return new SQSQueue($uri);
        } else if ($parts['scheme'] == 'beanstalk') {
            return new BeanstalkQueue($uri);
        } else {
            throw new \Exception('Unsupported uri scheme');
        }
    }
}
