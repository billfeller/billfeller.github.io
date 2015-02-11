<?php

// 踩坑记录
// 强类型判断
// string 中的字符可以通过一个从 0 开始的下标，用类似 array 结构中的方括号包含对应的数字来访问和修改，比如 $str[42]。
// 可以把 string 当成字符组成的 array。函数 substr() 和 substr_replace() 可用于操作多于一个字符的情况。 
// 默认类型转换：字符串->整数
// 该字符串的开始部分决定了它的值。如果该字符串以合法的数值开始，则使用该数值。否则其值为 0（零）。合法数值由可选的正负号，后面跟着一个或多个数字（可能有小数点），再跟着可选的指数部分。指数部分由 'e' 或 'E' 后面跟着一个或多个数字构成。 

$arr = array('result' => 1);
var_dump(isset($arr['result']));

$str = 'NO_DATA';
var_dump(isset($str['result']));
var_dump($str['result']);
var_dump(isset($str) && isset($str['result']));

