<?php
namespace timer\Lib;

interface LibInterface
{
    const EV_TIMER      = 1;
    const EV_TIMER_ONCE = 2;
    public function add($fd, $func, $flag = true, $args = null);
    public function del($fd);
    public function clearAllTimer();
    public function loop();
    public function getTimerCount();
}
