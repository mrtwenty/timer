# timer
php定时器，参考了workerman源码，由于workerman源码太过复杂， 故而抽取了一些出来，重新整理出来，实现一个单进程(守护进程)的定时器。

##原理
1. 利用pcntl，守护进程化
2. 利用stream_select的超时机制，来实现sleep，如果有event扩展的话，优先使用event扩展
3. 定时器是时间堆的方式实现，利用php的spl的优先队列

