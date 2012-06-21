<?php

/**
 * Constants are needed for the app
 * 
 */

define('DBHOST', 'localhost');
define('DBNAME', 'slimphpboiler');
define('DBUSER', 'root');
define('DBPASS', 'root');
define('BASEURL', 'http://localhost:8888/slim-boilerplate/');
// grab one from https://api.wordpress.org/secret-key/1.1/salt/
define('SECRET', '/Yo~G{.cpjxw4h9g@9>WuYHwy3a(2$q{i:3GOwq:buQ@O050R!|fV~}a<g_s}#*W');


date_default_timezone_set('America/Los_Angeles');

/**
 * SlimPHP for all the magic
 * 
 */
require_once(COREPATH . 'Slim/Slim.php');


/**
 * TwigView - for templating magic
 */
require_once(COREPATH . 'Views/TwigView.php');
// Configure
TwigView::$twigDirectory = COREPATH . '/Twig/lib/Twig/';


/**
 * Idiorm & Paris are light ORM/ActiveRecord that makes 
 * life with the Database lite and easy!
 */
require_once(COREPATH . 'libs/idiorm.php');
require_once(COREPATH . 'libs/paris.php');
// Configure
ORM::configure('mysql:host='.DBHOST.';dbname='.DBNAME);
ORM::configure('username', DBUSER);
ORM::configure('password', DBPASS);
ORM::configure('logging', true);
// ORM::configure('setting_name', 'value_for_setting');
// ORM::configure('id', 'primary_key');
// ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));



/**
 *  Instantiante Slim instance
 */ 
$app = new Slim(
    array(
        'view' => new TwigView, 
        'templates.path' => APPATH.'/templates',
        'mode' => 'prod'
    )
);

/**
 *  Setup Sessions with Slim
 */ 
$app->add( new Slim_Middleware_SessionCookie(), array(
    'expires' => '20 minutes',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'httponly' => false,
    'name' => 'slim_session',
    'secret' => SECRET,
    'cipher' => MCRYPT_RIJNDAEL_256,
    'cipher_mode' => MCRYPT_MODE_CBC
));

/**
 * Production mode config settings
 * 
 */
function mode_prod(){
    global $app;
    $app->config(array(
        'log.enable' => true,
        'log.path' => ABSPATH.'logs',
        'debug' => false
    ));
}
$app->configureMode('prod', 'mode_production');
/**
 * Development mode config settings
 * 
 */
function mode_dev(){
    global $app;
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
}

$app->configureMode('dev', 'mode_development');

