<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
 
/**
 * @file     ISign.php
 * @version  1.0
 * @author   wade.zhan
 * @date     2014-12-21 21:01:04
 * @desc     基于Redis bitmap实现签到功能
 */

/**
 * 每当用户在某一天上线的时候,我们就使用SETBIT,以用户名作为key,
 * 将那天所代表的网站的上线日作为offset参数,并将这个offset上的为设置为1.
 * 比如,如果今天是网站上线的第100天,而用户$uid=10001在今天阅览过网站,
 * 那么执行命令SETBIT peter 100 1.
 * 如果明天$uid=10001也继续阅览网站,那么执行命令SETBIT peter 101 1 ,以此类推. 
 * 当要计算$uid=10001总共以来的上线次数时,就使用BITCOUNT命令: 
 * 执行BITCOUNT $uid=10001 ,得出的结果就是$uid=10001上线的总天数. 
 * 签到后如果需要奖励判断可以另存key(uid:reward:day),里面可以存储对应的奖励及领奖标记位. 
 */

class ISign {
    const START_TIMESTRAMP = 1419091200; // 首日签到时间 20141221

    private $redis = NULL;

    public function __construct($config) {
        $this->redis = new Redis();
        $this->redis->connect($config['host'], $config['port'], $config['timeout'], NULL);
    }

    public function getSignKey($uid) {
        return sprintf('sign:%d', $uid);
    }

    public function sign($uid, $now = NULL) {
        if ($now === NULL) {
            $now = time();
        }
        $offset = intval(($now - self::START_TIMESTRAMP) / 86400) + 1;
        $signKey = $this->getSignKey($uid);
        return $this->redis->setBit($signKey, $offset, 1);
    }

    public function getSign($uid, $now = NULL) {
        if ($now === NULL) {
            $now = time();
        }
        $offset = intval(($now - self::START_TIMESTRAMP) / 86400) + 1;
        $signKey = $this->getSignKey($uid);
        return $this->redis->getBit($signKey, $offset);
    }

    public function getSignCount($uid) {
        $signKey = $this->getSignKey($uid);
        return $this->redis->bitCount($signKey);
    }
}

/* 测试用例 */
$config = array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'timeout' => 1,
);
$sign = new ISign($config);

$uid = 10086;
for ($i = 1; $i <= 15; $i ++) {
    $now = ISign::START_TIMESTRAMP + $i * 86400;
    $ret = $sign->sign($uid, $now);
    echo 'sign:'.$ret.PHP_EOL;

    $getValue = $sign->getSign($uid, $now);
    echo 'getSign:'.$getValue.PHP_EOL;
}

$count = $sign->getSignCount($uid);
var_dump($count);

