<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
 
/**
 * @file     cache.php
 * @version  1.0
 * @author   wade
 * @date     2014-12-12 11:00:58
 */

class Utils {
    const CACHED_STRATEGY_FACETOR_PRIFIX = 'csf';

    public static function getCSTKey($key, $factor) {
        return self::CACHED_STRATEGY_FACETOR_PRIFIX . '_' . $key . '_' . $factor;
    }

    public static function getCSTLockKey($key, $factor) {
        return self::CACHED_STRATEGY_FACETOR_PRIFIX . '_lock_' . '_' . $key . '_' . $factor;
    }

    /**
     * cache失效时只透传一定比例的请求到后端子系统
     * @param $method string 方法名
     * @param $params array  参数列表
     * @param $key    string 缓存key
     */
    public static function cacheStrategy($method, $params, $key, $factor = 10, $expire = 100) {
        $csfKey = self::getCSTKey($key, $factor);
        $memcached = new Memcached();
        $memcached->addServer('127.0.0.1', 11211);
        $value = $memcached->get($csfKey);
        if (!empty($value) && !empty($value['data']) && !empty($value['doubleExpire'])) {
            $doubleExpire = $value['doubleExpire'];
            if (time() <= $doubleExpire - $expire) { // cache还在[start, start + expire]时间内仍有效，无需请求后端
                return $value['data'];
            }

            $possible = mt_rand(1, 100);
            if ($possible > $factor) {
                return $value['data'];
            }
        }

        // cache已过期或者cache在(start + expire, +inf)，透传比例为factor的请求到后台实时请求并更新cache
        $data = self::$method($params);
        // 缓存请求成功的数据
        if (isset($data['resultCode']) && $data['resultCode'] == 0) {
            unset($data['resultCode']);
            $doubleExpire = time() + 2 * $expire;
            $value = array(
                'data' => $data,
                'doubleExpire' => $doubleExpire,
            );
            $ret = $memcached->set($csfKey, $value, $doubleExpire);
        }
        return $data;
    }

    /**
     * 通过加锁机制在cache失效时并发时只透传一个请求到后端子系统
     * @param $method string 方法名
     * @param $params array  参数列表
     * @param $key    string 缓存key
     */
    public static function cacheStrategyOnce($method, $params, $key, $expire = 100) {
        // 做法相似，只是需要把概率判断的算法换成使用锁保证高并发请求时严格只有一个请求透传到后端系统
        $csfKey = self::getCSTKey($key, 1);
        $lockKey = self::getCSTLockKey($key, 1);

        $memcached = new Memcached();
        $memcached->addServer('127.0.0.1', 11211);
        $value = $memcached->get($csfKey);
        if (!empty($value) && !empty($value['data']) && !empty($value['doubleExpire'])) {
            $doubleExpire = $value['doubleExpire'];
            if (time() <= $doubleExpire - $expire) { // cache还在[start, start + expire]时间内仍有效，无需请求后端
                return $value['data'];
            }

            $isLock = $memcached->get($lockKey);
            if (!empty($lockKey)) { // 有锁,表示当前已有请求到后端去读取最新数据并更新cache；
                return $value['data'];
            }
        }

        // cache已过期或者无锁，到后台实时请求并更新cache
        $ret = $memcached->set($lockKey, 1, 1);
        $data = self::$method($params);
        if (isset($data['resultCode']) && $data['resultCode'] == 0) {
            unset($data['resultCode']);
            $doubleExpire = time() + 2 * $expire;
            $value = array(
                'data' => $data,
                'doubleExpire' => $doubleExpire,
            );
            $ret = $memcached->set($csfKey, $value, $doubleExpire);
        }
        $ret = $memcached->delete($lockKey, 1);
        return $data;

    }

    public static function getProductDetail($params) {
        // TODO: 参数处理

        // todo : 请求后端接口
        // $data = array();
        // if (isset($data['ret']) && $data['ret'] == 0) {
        //     $data['resultCode'] = 0;
        // } else {
        //     $data['resultCode'] = -1;
        // }

        return array(
            'resultCode' => 0,
            'product_name' => '测试',
        );
    }
}

// 测试用例
$method = 'getProductDetail';
$params = array(
    'productId' => 10001,
);
$key = 'pid_10001';

$data = Utils::cacheStrategy($method, $params, $key);
var_dump($data);

// Utils::cacheStrategyOnce($method, $params, $key);
