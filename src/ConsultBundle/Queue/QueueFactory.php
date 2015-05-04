<?php

namespace ConsultBundle\Queue;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Queue Factory
 */
class QueueFactory extends ContainerAware
{
    /**
     * Get Queue Object
     *
     * @return AbstractQueue
     */
    public function get()
    {
        $uri = $this->container->getParameter('queue.uri');
        $sentryDsn = $this->container->getParameter('sentry.dsn');
        $parts = parse_url($uri);
        if (($parts['scheme'] == 'https' || $parts['scheme'] == 'http') &&
            ($hParts = explode('.', $parts['host'], 3)) &&
            ($hParts[0] == 'sqs' && $hParts[2] == 'amazonaws.com')) {
            return new SQSQueue($uri, $sentryDsn);
        } else if ($parts['scheme'] == 'beanstalk') {
            return new BeanstalkQueue($uri);
        } else {
            throw new \Exception('Unsupported uri scheme');
        }
    }
}
