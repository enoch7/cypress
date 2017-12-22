<?php
/**
* 
*/
class RedisTool
{
	private $redis;

	function __construct()
	{
		$this->init();
	}

	public function init()
	{
		$redis = new \Redis();
		$redis->connect('127.0.0.1',7001);
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


	/**
	 * lock, fully atomic
	 * @param  [string]  $key
	 * @param  [int]  $expire  
	 * @param  integer $timeout 
	 * @return [string]     [lock key value]
	 */
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


	/**
	 * unlock, make sure present process is still the owner of lock;
	 * @return [bool]        
	 */
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