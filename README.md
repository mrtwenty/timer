# timer
php定时器，参考了workerman源码，由于workerman源码太过复杂， 故而抽取了一些出来，重新整理出来，实现一个单进程(守护进程)的定时器。

##原理
1. 利用pcntl，守护进程化
2. 利用stream_select的超时机制，来实现sleep，如果有event扩展的话，优先使用event扩展
3. 定时器是时间堆的方式实现，利用php的spl的优先队列

##使用方式
1.安装
```
composer require mrtwenty/timer
```
2.编写index.php
```php
<?php
require __DIR__ . '/vendor/autoload.php';
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
```
3. 在cli环境上执行:
```
php index.php
```


