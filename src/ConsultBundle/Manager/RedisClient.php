<?php

namespace ConsultBundle\Manager;

/*
 * Redis Client
 */
class RedisClient
{
	/*
	 * Constructor
	 */
	public function __construct($snc_redis)
	{
		$this->redis = $snc_redis;
	}

	/*
	 * Get value by key
	 *
	 * @param string   $key    - Get Value in Redis by this key
	 *
	 * return value
	 */
	public function getKey($key)
	{
		if ($this->redis->exists($key)){
			return $this->redis->get($key);
		}
		return null;
	}

	/*
	 * Set value against given key
	 *
	 * @return null
	 */
	public function setKey($key, $value)
	{
		$this->redis->set($key, $value);
	}
}
