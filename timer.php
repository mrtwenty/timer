<?php
namespace timer;

namespace timer\lib\Select;

namespace timer\lib\Event;

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
