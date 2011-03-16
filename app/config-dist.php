<?php
/**
 * This configuration file sets system variables and constants for use throughout the application. 
 */


/**
 * Application constants
 */
define('ERROR_REPORTING_LEVEL', E_ERROR); /* set to E_ERROR for production environments */
define('DEBUG_MODE', false); /* set to false for production environments */
define('URL_VARS_KEY', 'url_vars_string'); /* this must match the name of the key that the mod_rewrite sends URL params under */
define('DISABLE_CRON_SCRIPT', false); /* set to false for production environments */
define('MAINTENANCE_MODE', false); /* puts the site into maintenance mode and serves up the maintenance page */
define('MAINTENANCE_ENDTIME', '2/22/11 1pm'); /* time maintenance is set to end (mm/dd/yy hh:mm am/pm) */


/**
 * Sandbox Account
 */
define('SANDBOX_ACCT_ENABLE', true); /* set to false for production environments */
define('SANDBOX_ACCT_USER', 'sandbox');
define('SANDBOX_ACCT_PASS', 'MRM3eJaPPKlcScCp80AD');


/**
 * Server paths
 */
define('SERVER_ROOT', '/var/www/syllabus/'); // terminate with '/'
define('APP_ROOT', '/var/www/syllabus/app/'); // terminate with '/'
define('WEB_ROOT', '/'); // terminate with '/'
define('PROTOCOL', (!empty($_SERVER['HTTPS'])) ? 'https://' : 'http://');
define('HOST', $_SERVER['HTTP_HOST']);
define('BASEHREF', PROTOCOL . HOST . WEB_ROOT);


/**
 * Smarty path constants
 */
define('SMARTY_LIB_DIR', '/usr/share/php/smarty/');
define('SMARTY_CACHE_DIR', '/usr/share/php/smarty/cache/');
define('SMARTY_CONFIGS_DIR', '/usr/share/php/smarty/configs/');
define('SMARTY_TEMPLATES_C_DIR', '/usr/share/php/smarty/templates_c/');
define('SMARTY_TEMPLATES_DIR', APP_ROOT . 'views/');


// append needed directories to the include path
$include_path = ini_get('include_path');
$include_path .= PATH_SEPARATOR . APP_ROOT;
$include_path .= PATH_SEPARATOR . SMARTY_LIB_DIR;
ini_set('include_path', $include_path);



/**
 * Form submission
 */
define('SUBMIT_TOKEN', session_id());
define('SUBMIT_TOKEN_HTML', '<input type="hidden" name="submit_token" value="' . SUBMIT_TOKEN . '" />');
if(isset($_POST['submit_token'])) {
    define('SUBMIT_TOKEN_POSTED', $_POST['submit_token']);
}
if(isset($_POST['command']) && is_array($_POST['command'])) {
    $commands = array_keys($_POST['command']);
    define('SUBMIT_METHOD', $commands[0]);
}


/**
 * CSS paths and compile settings.  Compiler will only compile local CSS files
 */
define('CSS_COMPILE', false);
define('CSS_COMPILE_DIR', 'compiled/');
define('CSS_COMPILE_FILE', 'compiled.css');
// the CSS_PATHS should only contain local CSS files.  Non-local CSS files should be manually added in the template
define('CSS_PATHS', '
    css/ajax.css,
    css/buttons.css,
    css/messages.css,
    css/ckeditor.css,
    css/colorbox.css,
    css/global.css,
    css/icons.css,
    css/navs.css,
    css/syllabus.css,
    css/tabs.css
');


/**
 * JS paths and compile settings.  Compiler will only compile local JS files
 */
define('JS_COMPILE', false);
define('JS_COMPILE_DIR', 'compiled/');
define('JS_COMPILE_FILE', 'compiled.js');
// the JS_PATHS should only contain local JS files.  Non-local JS files should be manually added in the template
define('JS_PATHS', '
    js/jquery.core.js,
    js/jquery.ui.js,
    js/ckeditor/ckeditor.js,
    js/jquery.colorbox.js,
    js/jquery.cookie.js,
    js/jquery.sort.js,
    js/jquery.tabs.js,
    js/syllabus.js,
');


/**
 * Shibboleth constants
 */
define('SHIB_IDP', 'https://idp-test.sfsu.edu');
define('SHIB_DIR', 'https://' . $_SERVER['HTTP_HOST'] . WEB_ROOT . 'shibboleth');
define('SHIB_SSO', 'https://' . $_SERVER['HTTP_HOST'] . WEB_ROOT . 'Shibboleth.sso');


/**
 * Database connection constants
 */
define('DB_HOST', 'localhost');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');


/**
 * SIMS anapshot directory for user database update
 */
define('SNAPSHOT_DIR', '/var/local/sims/');
