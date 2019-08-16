<?php
namespace timer;

use timer\Lib\Event;
use timer\Lib\Select;

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
