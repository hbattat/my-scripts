<?php

/**
 * SlimCore - A SlimPHP boilerplate
 * 
 */

define('DS', DIRECTORY_SEPARATOR);

/**
 * Commonly accessed paths
 */
if (!defined('ABSPATH')){
    define('ABSPATH', dirname(__FILE__) . '/');
}
// core dir
if (!defined('COREPATH')){
    define('COREPATH', ABSPATH . '_core/');
}
// app dir
if (!defined('APPATH')){
    define('APPATH', ABSPATH . 'app/');
}
// public assets dir
if (!defined('ASETPATH')){
    define('ASETPATH', ABSPATH . 'assets/');
}

require_once(ABSPATH . 'config.php');
require_once(APPATH . 'app.php');
