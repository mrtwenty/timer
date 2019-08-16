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

$timer->add(0.5, function () {

    if (Daemon::getOS() === OS_TYPE_WIN) {
        echo microtime_float() . "\n";
    } else {
        file_put_contents("/tmp/test.txt", microtime_float() . "\n", FILE_APPEND);
    }
});

$timer->add(1, function () {

    if (Daemon::getOS() === OS_TYPE_WIN) {
        echo microtime_float() . "once \n";
    } else {
        file_put_contents("/tmp/test.txt", microtime_float() . "once \n", FILE_APPEND);
    }
}, false);

$timer->loop();
