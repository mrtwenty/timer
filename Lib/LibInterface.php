<?php
namespace timer\lib;

interface LibInterface
{
    const EV_TIMER      = 1;
    const EV_TIMER_ONCE = 2;

    public function add($fd, $flag, $func, $args = null);
    public function del($fd, $flag);
    public function clearAllTimer();
    public function loop();
    public function getTimerCount();
}
