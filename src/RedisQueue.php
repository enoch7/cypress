<?php


class RedisQueue
{
	private  $redis;

	public function __construct($connection)
	{
		if ($connection->ping()) {
			$this->setKeeper($connection);	
		} else {
			throw new \Exception("redis connect error");
		}
	}

	public function setKeeper($redis)
	{
		$this->redis = $redis;
	}	

	public  function push($key, $value)
	{
		return $this->redis->lpush($key,$value);
	}

	public function pop($key, $waitSeconds = 0)
	{
		if (0 === $waitSeconds) {
			return $this->redis->rpop($key);
		} elseif ($waitSeconds > 0) {
			return $this->redis->brpop([$key],$waitSeconds);
		}
	}
}