<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
 
/**
 * @file     patterTest.php
 * @version  1.0
 * @author   wade
 * @date     2015-01-05 13:52:43
 */

// http://m.vip.com/beauty.html
// http://m.vip.com/brand-320723-0-0-0-1-0-1-40.html
// http://m.vip.com/preferential-brand-320489-0-0-0-1-0-1-40.html
// http://m.vip.com/hot-list-clothes-index.html
// http://m.vip.com/classify-list-9047-0-0-0-0-1-40.html
$pattern = '/(?:beauty\.html|brand(?:-[a-zA-Z0-9]+){8}\.html|preferential-brand.*?\.html|hot-list.*?\.html|classify-list.*?\.html)$/';

$referer = 'http://m.vip.com/beauty.html';
$isListRefer = preg_match($pattern, $referer);
var_dump($isListRefer);

$referer = 'http://m.vip.com/brand-320723-0-0-0-1-0-1-40.html';
$isListRefer = preg_match($pattern, $referer);
var_dump($isListRefer);

$referer = 'http://m.vip.com/preferential-brand-320489-0-0-0-1-0-1-40.html';
$isListRefer = preg_match($pattern, $referer);
var_dump($isListRefer);

$referer = 'http://m.vip.com/hot-list-clothes-index.html';
$isListRefer = preg_match($pattern, $referer);
var_dump($isListRefer);

$referer = 'http://m.vip.com/classify-list-9047-0-0-0-0-1-40.html';
$isListRefer = preg_match($pattern, $referer);
var_dump($isListRefer);

$referer = '';
$isListRefer = preg_match($pattern, $referer);
var_dump($isListRefer);

$referer = 'http://m.vip.com/preheating-brand-295227-0-0-1-40.html';
$isListRefer = strpos($referer, 'preheating-brand');
var_dump($isListRefer);