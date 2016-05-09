<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
 
/**
 * @file     twenproxyTest.php
 * @version  1.0
 * @author   wade
 * @date     2014-12-14 21:00:45
 */

/*
    /usr/local/nutcracker-0.3.0/conf/nutcracker.yml
    alpha:
      listen: 127.0.0.1:22121
      hash: fnv1a_64
      hash_tag: "{}"
      distribution: ketama
      auto_eject_hosts: false
      timeout: 400 
      redis: true
      servers:
       - 127.0.0.1:6379:1

    beta:
      listen: 127.0.0.1:22122
      hash: fnv1a_64
      distribution: ketama
      timeout: 400 
      backlog: 1024
      preconnect: true
      auto_eject_hosts: true
      server_retry_timeout: 2000
      server_failure_limit: 3
      servers:
       - 127.0.0.1:11211:1
       - 127.0.0.1:11212:1
*/

$redis = new Redis();
$redis->connect('127.0.0.1', 22121, 1);

$key1 = 'twenproxy1';
$result = $redis->set($key1, 1);
var_dump($result);
$result = $redis->get($key1);
var_dump($result);
$result = $redis->delete($key1);
var_dump($result);
$result = $redis->get($key1);
var_dump($result);

$key2 = 'twenproxy2';
$memcached = new Memcached();
$memcached->addServer('127.0.0.1', 22122);
$result = $memcached->set($key2, 2);
var_dump($result);
$result = $memcached->get($key2);
var_dump($result);

$key3 = 'twenproxy3';
$result = $memcached->set($key3, 3);
var_dump($result);
$result = $memcached->get($key3);
var_dump($result);
$result = $memcached->delete($key3);
var_dump($result);
$result = $memcached->get($key3);
var_dump($result);