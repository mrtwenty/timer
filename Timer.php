<?php
namespace timer;

use timer\lib\Event;
use timer\lib\Select;

class Timer
{
    public static function factory()
    {
        if (extension_loaded('event')) {
            return new Event;
        }

        return new Select();
    }
}
