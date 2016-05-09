<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
 
/**
 * @file     UserTj.php
 * @version  1.0
 * @author   wade
 * @date     2014-12-13 21:50:13
 * @desc     基于Sort Set实现活跃用户相关统计
 *           1. 实时统计今天活跃用户；
 *           2. 实时统计留存率；
 */

class UserTj {
    private $redis;

    public function __construct($config) {
        $this->redis = new Redis();
        $this->redis->connect($config['host'], $config['port'], $config['timeout'], NULL);
    }

    public function getRedisKey($timestamp) {
        return sprintf('play:%s', date('Y-m-d', $timestamp));
    }

    /**
     * 设置今日活跃用户
     */
    public function setActiveUser($userId, $timestamp = NULL) {
        if (empty($timestamp)) {
            $timestamp = time();
        }

        $activeKey = $this->getRedisKey($timestamp);
        return $this->redis->sAdd($activeKey, $userId);
    }

    /**
     * 实时查询指定日期的活跃用户数
     */
    public function queryDAU($timestamp) {
        $activeKey = $this->getRedisKey($timestamp);
        return $this->redis->sSize($activeKey);
    }

    /**
     * 实时查询指定连续期内的总活跃用户数(去重)
     */
    public function queryAllActiveUsers($begin, $end) {
        $members = array();
        for ($time = $begin; $time <= $end; $time += 86400) {
            $activeKey = $this->getRedisKey($time);
            $tMembers = $this->redis->sMembers($activeKey);
            $members = array_merge($members, $tMembers);
            $members = array_unique($members);
        }

        return count($members);
    }

    /**
     * 实时计算用户留存率指标
     * 1. 次日留存率
     * 2. 第3日留存率
     * 3. 第7日留存率
     * 4. 第30日留存率
     */
    public function queryActiveUserListLastMonth($timestamp) {
        $start = $this->getRedisKey($timestamp);
        $tomorrow = $this->getRedisKey(strtotime('+1 day', $timestamp));
        $theDayAfterTomorrow = $this->getRedisKey(strtotime('+2 day', $timestamp));
        $theDayAfterWeek = $this->getRedisKey(strtotime('+6 day', $timestamp));
        $theDayAfterMonth = $this->getRedisKey(strtotime('+1 month', $timestamp));

        $members1 = $this->redis->sMembers($start);
        $members2 = $this->redis->sMembers($tomorrow);
        $members3 = $this->redis->sMembers($theDayAfterTomorrow);
        $members7 = $this->redis->sMembers($theDayAfterWeek);
        $members30 = $this->redis->sMembers($theDayAfterMonth);

        $tjList = array();
        $tjList['tomorrow'] = array_intersect($members1, $members2);
        $tjList['theDayAfterTomorrow'] = array_intersect($members1, $members3);
        $tjList['theDayAfterWeek'] = array_intersect($members1, $members7);
        $tjList['theDayAfterMonth'] = array_intersect($members1, $members30);;

        return $tjList;
    }
}

// 测试用例
$redisConfig = array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'timeout' => 1,
);
$userTj = new UserTj($redisConfig);

// 1418483295

$uidList = array(10001, 10002, 10003, 10004, 10005, );
foreach ($uidList as $uid) {
    $userTj->setActiveUser($uid, 1418483295 - 6 * 86400);
}

$uidList = array(10001, 10002, 10003, 10004, 10005, 10006, 10007, 10008, );
foreach ($uidList as $uid) {
    $userTj->setActiveUser($uid, 1418483295 - 5 * 86400);
}

$uidList = array(10001, 10002, 10003, 10004, 10005, 10006, 10008, );
foreach ($uidList as $uid) {
    $userTj->setActiveUser($uid, 1418483295 - 4 * 86400);
}

$uidList = array(10001, 10002, 10003, 10004, 10005, 10008, );
foreach ($uidList as $uid) {
    $userTj->setActiveUser($uid, 1418483295 - 3 * 86400);
}

$uidList = array(10001, 10002, 10003, 10004, 10005, 10008, 10009, 10010, 10011, );
foreach ($uidList as $uid) {
    $userTj->setActiveUser($uid, 1418483295 - 2 * 86400);
}

$uidList = array(10001, 10002, 10003, 10004, 10005, 10008, 10009, 10010, 10011, 10012);
foreach ($uidList as $uid) {
    $userTj->setActiveUser($uid, 1418483295 - 86400);
}

$uidList = array(10001, 10002, 10004, 10005, 10008, 10009, 10010, 10011, 10012, 10013, 10014);
foreach ($uidList as $uid) {
    $userTj->setActiveUser($uid, 1418483295);
}


$count = $userTj->queryDAU(1418483295 - 86400);
var_dump($count);

$count = $userTj->queryDAU(1418483295);
var_dump($count);

$count = $userTj->queryAllActiveUsers(1418483295 - 86400, 1418483295);
var_dump($count);

$count = $userTj->queryAllActiveUsers(1418483295 - 6 * 86400, 1418483295);
var_dump($count);

$count = $userTj->queryActiveUserListLastMonth(1418483295 - 6 * 86400);
var_dump($count);