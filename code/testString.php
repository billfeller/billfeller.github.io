<?php

// 踩坑记录
// 强类型判断

$arr = array('result' => 1);
var_dump(isset($arr['result']));

$str = 'NO_DATA';
var_dump(isset($str['result']));
var_dump($str['result']);
var_dump(isset($str) && isset($str['result']));

