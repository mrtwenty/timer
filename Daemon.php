<?php
namespace timer;

use timer\Timer;

class Daemon
{
    public static $stdoutFile = '/dev/null';
    public static $daemonName = 'daemonPHP';
    public static function runAll()
    {
        self::checkEnvCli(); //检查环境
        self::daemonize(); //守护进程化
        self::chdir(); //改变工作目录
        self::closeSTD(); //关闭标准输出、标准错误
        self::setProcessTitle(self::$daemonPHP); //设置守护进程的名字
        return Timer::factory();
    }

    /**
     * 检测执行环境，必须是linux系统和cli方式执行
     * @return [type] [description]
     */
    protected static function checkEnvCli()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            exit("must be Linux\n");
        }

        if (php_sapi_name() != "cli") {
            exit("only run in command line mode \n");
        }
    }

    /**
     * 设置掩码、fork两次、设置会话组长
     * @return [type] [description]
     */
    protected static function daemonize()
    {
        umask(0);
        $pid = pcntl_fork();
        if (-1 === $pid) {
            throw new Exception('fork fail');
        } elseif ($pid > 0) {
            exit(0);
        }
        if (-1 === posix_setsid()) {
            throw new Exception("setsid fail");
        }
        // Fork again avoid SVR4 system regain the control of terminal.
        $pid = pcntl_fork();
        if (-1 === $pid) {
            throw new Exception("fork fail");
        } elseif (0 !== $pid) {
            exit(0);
        }
    }

    /**
     * 改变工作目录
     * @return [type] [description]
     */
    protected static function chdir()
    {
        if (!chdir('/')) {
            throw new Exception("change dir fail", 1);
        }
    }

    /**
     * 关闭标准输出、标准错误
     * @return [type] [description]
     */
    protected static function closeSTD()
    {
        //定义两个全局变量
        global $STDOUT, $STDERR;
        $handle = fopen(static::$stdoutFile, "a");
        if ($handle) {
            unset($handle);
            set_error_handler(function () {});
            fclose($STDOUT);
            fclose($STDERR);
            fclose(STDOUT);
            fclose(STDERR);
            $STDOUT = fopen(static::$stdoutFile, "a");
            $STDERR = fopen(static::$stdoutFile, "a");

            restore_error_handler();
        } else {
            throw new Exception('can not open stdoutFile ' . static::$stdoutFile);
        }
    }

    /**
     * 设置定时器名字
     *
     * @param string $title
     * @return void
     */
    protected static function setProcessTitle($title)
    {
        set_error_handler(function () {});
        // >=php 5.5
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($title);
        } // Need proctitle when php<=5.5 .
        elseif (extension_loaded('proctitle') && function_exists('setproctitle')) {
            setproctitle($title);
        }
        restore_error_handler();
    }

}
