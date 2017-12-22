<?php
require_once dirname(__DIR__) . "/src/RedisQueue.php";

$redis = new \Redis();
$redis->connect('127.0.0.1','7001');
$queue = new RedisQueue($redis);

$result = $queue->push('mylist', 11);
var_dump($result);

$result = $queue->pop('mylist', 10);

var_dump($result);