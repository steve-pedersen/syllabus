<?php

/**
 * This file handles all basic setup needed for the application including setting constants (both inline and
 * from the application's config.php file), handling the session, initial url parsing, and dispatching the
 * request to the appropriate controller
 */


// start the session
session_start();


/**
 * Require the application's config file.  This should reside in the same directory location as this (init.php) file
 * The config.php file will set all server-specific data which will be used later to construct the framework environment
 */
require_once 'config.php';

// set the appropriate error reporting level
ini_set('display_errors', true);
error_reporting(ERROR_REPORTING_LEVEL);



/**
 * Autoload magic function.  This function will tell PHP how to autoload classes when they are instantiated.
 */
function __autoload($class_name) {
    switch(true) {
        // Models
        case preg_match('!Model!', $class_name):
            $class_file = 'models/' . $class_name . '.class.php';
            break;
        
        // Controllers
        case preg_match('!Controller!', $class_name):
            $class_file = 'controllers/' . $class_name . '.class.php';
            break;
        
        // Other classes
        default:
            $class_file = '_helpers/' . $class_name . '.class.php';
            break;
    }
    // require the file
    require_once $class_file;
}


/**
 * Set variables based on the URL
 */
$url_vars_array = array();
$controller_name = 'IndexController';
$current_url = '';

if(isset($_GET)) {
    // remove the url string from the $_GET array and set its value as $url_vars_array
    if(isset($_GET[URL_VARS_KEY])) {
        $current_url .= $_GET[URL_VARS_KEY];
        $url_vars_array = explode('/', $_GET[URL_VARS_KEY]);
        unset($_GET[URL_VARS_KEY]);
    }    
    // set the name of the controller for this request
    if(isset($url_vars_array[0])) {
        $prefix = ucwords(strtolower($url_vars_array[0]));
        $controller_name = (file_exists(APP_ROOT . 'controllers/' . $prefix . 'Controller.class.php'))
            ? $prefix . 'Controller'
            : 'IndexController';
    }
    // recreate a query string from the remaining $_GET data
    if(count($_GET)) {
        foreach($_GET as $k => $v) {
            $query_array[] = $k . '=' . $v;
        }
        $current_url .= '?' . implode('&', $query_array);
    }
}
// set the CURRENT_URL constant: contains the current URL path and query string
define('CURRENT_URL', $current_url);



/**
 * Instantiate the controller, set the url_vars, and initialize the request
 */
$C = new $controller_name;
$C->url_vars = $url_vars_array;
$C->init();