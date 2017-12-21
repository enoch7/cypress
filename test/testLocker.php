<?php
require_once dirname(__DIR__) . "/src/RedisLocker.php";

$redis = new \Redis();
$redis->connect('127.0.0.1','7001');
$locker = new RedisLocker($redis);

$result = $locker->lock('mylock', 10, 2);
var_dump($result);

$result = $locker->unlock('mylock' , $result);

var_dump($result);