<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
 
/**
 * @file     FriendGraph.php
 * @version  1.0
 * @author   wade
 * @date     2014-12-13 18:09:33
 */

class FriendGraph {
    private $redis;

    const FOLLOWS_KEY = 'F';
    const FOLLOWERS_KEY = 'f';
    const BLOCKS_KEY = 'B';
    const BLOCKED_KEY = 'b';

    public function __construct($config) {
        $this->redis = new Redis();
        $result = $this->redis->connect($config['host'], $config['port'], $config['timeout']);
    }

    public function follow($fromUser, $toUser) {
        $forwardKey = sprintf('%s:%s', self::FOLLOWS_KEY, $fromUser);
        $forwardResult = $this->redis->sadd($forwardKey, $toUser);
        $reverseKey = sprintf('%s:%s', self::FOLLOWERS_KEY, $toUser);
        $reverseResult = $this->redis->sadd($reverseKey, $fromUser);
        return $forwardResult && $reverseResult;
    }

    public function unfollow($fromUser, $toUser) {
        $forwardKey = sprintf('%s:%s', self::FOLLOWS_KEY, $fromUser);
        $forwardResult = $this->redis->srem($forwardKey, $toUser);
        $reverseKey = sprintf('%s:%s', self::FOLLOWERS_KEY, $toUser);
        $reverseResult = $this->redis->srem($reverseKey, $fromUser);
        return $forwardResult && $reverseResult;
    }

    public function block($fromUser, $toUser) {
        $forwardKey = sprintf('%s:%s', self::BLOCKS_KEY, $fromUser);
        $forwardResult = $this->redis->sadd($forwardKey, $toUser);
        $reverseKey = sprintf('%s:%s', self::BLOCKED_KEY, $toUser);
        $reverseResult = $this->redis->sadd($reverseKey, $fromUser);
        return $forwardResult && $reverseResult;
    }

    public function unblock($fromUser, $toUser) {
        $forwardKey = sprintf('%s:%s', self::BLOCKS_KEY, $fromUser);
        $forwardResult = $this->redis->srem($forwardKey, $toUser);
        $reverseKey = sprintf('%s:%s', self::BLOCKED_KEY, $toUser);
        $reverseResult = $this->redis->srem($reverseKey, $fromUser);
        return $forwardResult && $reverseResult;
    }

    public function getFollows($user) {
        $follows = $this->redis->smembers(sprintf('%s:%s', self::FOLLOWS_KEY, $user));
        $blocked = $this->redis->smembers(sprintf('%s:%s', self::BLOCKED_KEY, $user));
        return array_diff($follows, $blocked);
    }

    public function getFollowers($user) {
        $followers = $this->redis->smembers(sprintf('%s:%s', self::FOLLOWERS_KEY, $user));
        $blocks = $this->redis->smembers(sprintf('%s:%s', self::BLOCKS_KEY, $user));
        return array_diff($followers, $blocks);
    }

    public function getBlocks($user) {
        return $this->redis->smembers(sprintf('%s:%s', self::BLOCKS_KEY, $user));
    }

    public function getBlocked($user) {
        return $this->redis->smembers(sprintf('%s:%s', self::BLOCKED_KEY, $user));
    }
}

// 测试用例
// $redisConfig = array(
//     'host'    => '127.0.0.1',
//     'port'    => 6379,
//     'timeout' => 5,
// );
// $friendGraph = new FriendGraph($redisConfig);

// $me = 10001;
// $follows = array(10002, 10003, 10004, 10005, 10006);

// foreach ($follows as $toUser) {
//     $friendGraph->follow($me, $toUser);
//     $friendGraph->block($toUser, $me);
// }

// $followsList = $friendGraph->getFollows($me);
// $blocksList = $friendGraph->getBlocks($me);
// var_dump($followsList);
// var_dump($blocksList);
