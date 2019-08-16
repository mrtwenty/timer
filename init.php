<?php

//开启错误
ini_set('display_errors', 'on');
//定义错误登记
error_reporting(E_ALL);

//重置opcache
if (function_exists('opcache_reset')) {
    opcache_reset();
}

//定义常量
define('OS_TYPE_LIN', 'linux');
define('OS_TYPE_WIN', 'windows');

//兼容PHP7
if (!class_exists('Error')) {
    class Error extends Exception
    {
    }
}
