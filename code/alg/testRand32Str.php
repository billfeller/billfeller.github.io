<?php

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

