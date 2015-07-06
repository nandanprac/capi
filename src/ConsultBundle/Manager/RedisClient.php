<?php

namespace ConsultBundle\Manager;

/**
 * Redis Client
 */
class RedisClient
{
    private $redis;
    /**
     * Constructor
     *
     * @param string $sncRedis
     */
    public function __construct($sncRedis)
    {
        $this->redis = $sncRedis;
    }

    /**
     * Get value by key
     *
     * @param string $key - Get Value in Redis by this key
     *
     * @return value
     */
    public function getKey($key)
    {
        $key = strtolower($key);
        if ($this->redis->exists($key)) {
            return $this->redis->get($key);
        }

        return null;
    }

    /**
     * Set value against given key
     *
     * @param string $key   - Get Value in Redis by this key
     * @param string $value - Get Value in Redis by this key
     *
     * @return null
     */
    public function setKey($key, $value)
    {
        $key = strtolower($key);
        $this->redis->set($key, $value);
    }

    /**
     * If the give key exists in redis
     *
     * @param string $key - Get Value in Redis by this key
     *
     * @return boolean
     */
    public function keyExists($key)
    {
        $key = strtolower($key);

        return $this->redis->exists($key) ? true : false;
    }
}
