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

$timer->add(0.5, Timer::EV_TIMER, function () {
    echo microtime_float() . "\n";
});

$timer->add(1, Timer::EV_TIMER_ONCE, function () {
    echo microtime_float() . "once \n";
});

//待删除的id
$id = $timer->add(5, Timer::EV_TIMER, function () {
    echo "clean up after running once\n";
});

//删除定时器
$timer->add(6, Timer::EV_TIMER_ONCE, function () use ($id, $timer) {
    $timer->del($id, Timer::EV_TIMER);
});

$timer->loop();
