<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
 
/**
 * @file     IBit.php
 * @version  1.0
 * @author   wade
 * @date     2014-12-21 18:54:51
 * @desc     统一封装二元标记位操作
 *           注意能使用的最大偏移量(即二元标记位)是2^29-1(536870911),
 *           因为Redis字符串的大小被限制在512M以内.
 *           如果你需要使用比这更大的空间,你可以使用多个key.
 */

class IBit {
    private $redis = NULL;

    public function __construct($config) {
        $this->redis = new Redis();
        $this->redis->connect($config['host'], $config['port'], $config['timeout'], NULL);
    }

    public function exists($key) {
        return $this->redis->exists($key);
    }

    public function getBit($key, $index) {
        return $this->redis->getBit($key, $index);
    }

    public function setBit($key, $index, $value = 0) {
        return $this->redis->setBit($key, $index, $value);
    }
}

/* 测试用例 */
$config = array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'timeout' => 1,
);
$bit = new IBit($config);

$uid = 10086;
$key = sprintf('bit:%d', $uid);
for ($i = 1; $i <= 100; $i ++) {
    $value = mt_rand(0, 1);
    $ret = $bit->setBit($key, $i, $value);
    echo 'setBit:'.$ret.PHP_EOL;

    $getValue = $bit->getBit($key, $i);
    echo 'getBit:'.$getValue.PHP_EOL;

    var_dump($value == $getValue);
}


