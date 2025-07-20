<?php
/**
 * Windows poly-fill for the POSIX signal constants + pcntl stubs.
 * Autoloaded via composer.json → autoload.files.
 */
if (PHP_OS_FAMILY === 'Windows') {
    // --- constants -------------------------------------------------
    defined('SIG_DFL')  || define('SIG_DFL', 0);
    defined('SIGINT')   || define('SIGINT', 2);
    defined('SIGTERM')  || define('SIGTERM', 15);
    defined('SIGHUP')   || define('SIGHUP', 1);
    defined('SIGUSR1')  || define('SIGUSR1', 10);
    defined('SIGUSR2')  || define('SIGUSR2', 12);

    // --- stubs -----------------------------------------------------
    if (!function_exists('pcntl_signal')) {
        function pcntl_signal($signal, $handler, $restart_syscalls = true)      { return true; }
        function pcntl_async_signals($enable = true)                            { return true; }
        function pcntl_signal_dispatch()                                        { return true; }
        function pcntl_signal_get_handler($signal)                              { return \SIG_DFL; }
        // rarely used but harmless placeholders:
        function pcntl_alarm($seconds)                                          { return 0; }
        function pcntl_unshare($flags)                                          { return false; }
    }
	
	  // ----- posix stubs (NEW) -----------------------------------------
    if (!function_exists('posix_kill')) {
        function posix_kill($pid, $sig) { return false; } // always fail harmlessly
        function posix_getpgid($pid = 0) { return $pid ?: getmypid(); }
    }
}