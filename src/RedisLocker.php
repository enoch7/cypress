<?php

/**
* 
*/
class RedisLocker
{
	private $redis;

	public function __construct($connection)
	{
		$this->setKeeper($connection);
	}

	public function setKeeper($redis)
	{
		$this->redis = $redis;
	}

	public function lock($key, $expire, $timeout = 0)
	{
		$random = uniqid();
		again:
		if ($this->redis->set($key, $random, ['NX', 'EX'=>$expire])) {
			return $random;
		} else if($timeout > 0) {
			$timeout--;
			sleep(1);
			goto again;
		}
		return false;
	}

	public function unlock($key, $value)
	{
		static $releaseLuaScript = <<<LUA
if redis.call("GET",KEYS[1])==ARGV[1] then
    return redis.call("DEL",KEYS[1])
else
    return 0
end
LUA;
		if ($this->redis->eval($releaseLuaScript, [$key,$value], 1)) {
			return true;	
		}
		return false;
	}

}