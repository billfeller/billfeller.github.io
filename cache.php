<?php

class Utils {
    const CACHED_STRATEGY_FACETOR_PRIFIX = 'csf';

    public static function getCSTkey ($key, $factor) {
        return self::CACHED_STRATEGY_FACETOR_PRIFIX . '_' . $key . '_' . $factor;
    }

    /**
     * @param $method string 方法名
     * @param $params array  参数列表
     * @param $key    string 缓存key
     */
    public static function cacheStrategy ($method, $params, $key, $factor = 10, $expire = 100) {
        $csfKey = self::getCSTkey($key, $factor);
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
        // todo: 只缓存请求成功的数据 需要过滤掉失败的情况
        if (isset($data['requestSucc']) && $data['requestSucc'] == 0) {
            unset($data['requestSucc']);
            $doubleExpire = time() + 2 * $expire;
            $value = array(
                'data' => $data,
                'doubleExpire' => $doubleExpire,
            );
            $ret = $memcached->set($csfKey, $value, $doubleExpire);
        }
        return $data;
    }

    public static function getProductDetail($params) {
        if (!is_array($params)) {
            $params = array($params);
        }

        // todo : 请求后端接口
        // $data = array();
        // if (isset($data['ret']) && $data['ret'] == 0) {
        //     $data['requestSucc'] = 0;
        // } else {
        //     $data['requestSucc'] = -1;
        // }

        return array(
            'requestSucc' => 0,
            'product_name' => '测试',
        );
    }
}


