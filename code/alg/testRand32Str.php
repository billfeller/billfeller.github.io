<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
 
/**
 * @file     testRand32Str.php
 * @version  1.0
 * @author   wade
 * @date     2015-01-21 23:38:17
 */

// 测试相应性能数据
// time /usr/local/php/bin/php testRand32Str.php

function rand32Str($len = 32) {
    $str = '0123456789abcdef';
    $strLen = strlen($str);

    $tmp = '';
    for ($i = 0; $i < $len; $i ++) {
        $tmp .= substr(str_shuffle($str), 0, 1);
    }

    return $tmp;
}

for ($i = 0; $i < 10000; $i ++) {
    $randStr = rand32Str();
    echo sprintf("%s %d\n", $randStr, strlen($randStr));
}

