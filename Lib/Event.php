<?php
namespace timer\Lib;

class Event implements LibInterface
{
    /**
     * Event base.
     * @var object
     */
    protected $eventBase = null;

    /**
     * All timer event listeners.
     * [func, args, event, flag, time_interval]
     * @var array
     */
    protected $eventTimer = array();

    /**
     * Timer id.
     * @var int
     */
    protected static $timerId = 1;

    /**
     * construct
     * @return void
     */

    public function __construct()
    {

        if (class_exists('\\\\EventBase', false)) {
            $class_name = '\\\\EventBase';
        } else {
            $class_name = '\EventBase';
        }
        $this->eventBase = new $class_name();
    }

    /**
     * @see EventInterface::add()
     */
    public function add($fd, $func, $flag = true, $args = array())
    {
        $flag = $flag === true ? self::EV_TIMER : self::EV_TIMER_ONCE;

        if (class_exists('\\\\Event', false)) {
            $class_name = '\\\\Event';
        } else {
            $class_name = '\Event';
        }

        $param = array($func, (array) $args, $flag, $fd, self::$timerId);
        $event = new $class_name($this->eventBase, -1, $class_name::TIMEOUT | $class_name::PERSIST, array($this, "timerCallback"), $param);
        if (!$event || !$event->addTimer($fd)) {
            return false;
        }
        $this->eventTimer[self::$timerId] = $event;
        return self::$timerId++;
    }

    public function del($fd)
    {
        if (isset($this->eventTimer[$fd])) {
            $this->eventTimer[$fd]->del();
            unset($this->eventTimer[$fd]);
        }
        return true;
    }

    /**
     * Timer callback.
     * @param null $fd
     * @param int $what
     * @param int $timer_id
     */
    public function timerCallback($fd, $what, $param)
    {
        $timer_id = $param[4];

        if ($param[2] === self::EV_TIMER_ONCE) {
            $this->eventTimer[$timer_id]->del();
            unset($this->eventTimer[$timer_id]);
        }

        try {
            call_user_func_array($param[0], $param[1]);
        } catch (\Exception $e) {
            exit(250);
        } catch (\Error $e) {
            exit(250);
        }
    }

    /**
     * @see Events\EventInterface::clearAllTimer()
     * @return void
     */
    public function clearAllTimer()
    {
        foreach ($this->eventTimer as $event) {
            $event->del();
        }
        $this->eventTimer = array();
    }

    /**
     * @see EventInterface::loop()
     */
    public function loop()
    {
        $this->eventBase->loop();
    }

    /**
     * Get timer count.
     *
     * @return integer
     */
    public function getTimerCount()
    {
        return count($this->eventTimer);
    }
}
