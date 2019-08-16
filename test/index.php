<?php
require __DIR__ . '/../vendor/autoload.php';
use timer\Daemon;

$timer = Daemon::runAll();

//测试执行 timer类

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return bcadd($usec, $sec, 3);
}

$timer->add(0.5, function ($i) {
    file_put_contents("/tmp/test.txt", microtime_float() . "\n");
});

$timer->add(1, function () {
    file_put_contents("/tmp/test.txt", microtime_float() . "once \n");
}, false);

$timer->loop();
