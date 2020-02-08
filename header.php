<?php
/*
 * define
 */
define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
/*
 * Function Definitions
 */
function disable_ob() {
    // Turn off output buffering
    ini_set('output_buffering', 'Off');
    // Turn off PHP output compression
    ini_set('zlib.output_compression', false);
    // Implicitly flush the buffer(s)
    ini_set('implicit_flush', true);
    set_time_limit(0);
    ob_start();
    ob_end_flush();
    ob_implicit_flush(1);
    // Clear, and turn off output buffering
    while (ob_get_level() > 0) {
        // Get the curent level
        $level = ob_get_level();
        // End the buffering
        ob_end_clean();
        // If the current level has not changed, abort
        if (ob_get_level() == $level) break;
    }
    // Disable apache output buffering/compression
//    if (function_exists('apache_setenv')) {
//        apache_setenv('no-gzip', '1');
//        apache_setenv('dont-vary', '1');
//    }
    // Disable Nginx
    header('Cache-Control: no-cache');         // 告知浏览器不进行缓存
    header('X-Accel-Buffering: no');           // 关闭加速缓冲
}
/*
 * Function Call
 */
// tell php to automatically flush after every output
// including lines of output produced by shell commands
session_start();
disable_ob();

