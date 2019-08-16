<?php
namespace timer\lib;

class Event implements LibInterface
{
    /**
     * Event base.
     * @var object
     */
    protected $_eventBase = null;

    /**
     * All listeners for read/write event.
     * @var array
     */
    protected $_allEvents = array();

    /**
     * Event listeners of signal.
     * @var array
     */
    protected $_eventSignal = array();

    /**
     * All timer event listeners.
     * [func, args, event, flag, time_interval]
     * @var array
     */
    protected $_eventTimer = array();

    /**
     * Timer id.
     * @var int
     */
    protected static $_timerId = 1;

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
        $this->_eventBase = new $class_name();
    }

    /**
     * @see EventInterface::add()
     */
    public function add($fd, $flag, $func, $args = array())
    {
        if (class_exists('\\\\Event', false)) {
            $class_name = '\\\\Event';
        } else {
            $class_name = '\Event';
        }

        $param = array($func, (array) $args, $flag, $fd, self::$_timerId);
        $event = new $class_name($this->_eventBase, -1, $class_name::TIMEOUT | $class_name::PERSIST, array($this, "timerCallback"), $param);
        if (!$event || !$event->addTimer($fd)) {
            return false;
        }
        $this->_eventTimer[self::$_timerId] = $event;
        return self::$_timerId++;
    }

    /**
     * @see Events\EventInterface::del()
     */
    public function del($fd, $flag)
    {
        if (isset($this->_eventTimer[$fd])) {
            $this->_eventTimer[$fd]->del();
            unset($this->_eventTimer[$fd]);
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
            $this->_eventTimer[$timer_id]->del();
            unset($this->_eventTimer[$timer_id]);
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
        foreach ($this->_eventTimer as $event) {
            $event->del();
        }
        $this->_eventTimer = array();
    }

    /**
     * @see EventInterface::loop()
     */
    public function loop()
    {
        $this->_eventBase->loop();
    }

    /**
     * Get timer count.
     *
     * @return integer
     */
    public function getTimerCount()
    {
        return count($this->_eventTimer);
    }
}
