<?php
/**
 * plex web viewer
 */

use Dotenv\Dotenv;
use Tracy\Debugger;
use Camoo\Config\Config;
use Plex\Core\PlexLoader;
use Plex\Database\PlexSql;
use Plex\Database\dbObject;
use Plex\Template\Template;
use Plex\Database\Loader\MetaSettings;

session_start();

// HOME=D:\\development\\plex_web
// APP_PATH D:\\development\\plex_web
define('__ROOT_DIRECTORY__', dirname(realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']), 1));

define('__COMPOSER_LIB__', __ROOT_DIRECTORY__.'/vendor');
set_include_path(get_include_path().\PATH_SEPARATOR.__COMPOSER_LIB__);

require_once __COMPOSER_LIB__.'/autoload.php';
// Debugger::enable();

// Debugger::$showLocation = Tracy\Dumper::LOCATION_SOURCE; // Shows path to where the dump() was called
// //Debugger::$logSeverity  = \E_WARNING | \E_NOTICE;
// Debugger::$dumpTheme    = 'dark';
// Debugger::$showBar      = true;          // (bool) defaults to true
// Debugger::$strictMode   = ~\E_DEPRECATED & ~\E_USER_DEPRECATED & ~\E_NOTICE;

// Debugger::$showLocation = Tracy\Dumper::LOCATION_CLASS | Tracy\Dumper::LOCATION_LINK; // Shows both paths to the classes and link to where the dump() was called
// Debugger::$showLocation = false; // Hides additional location information
// Debugger::$showLocation = true; // Shows all additional location information

$config = new Config(__ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'config.ini');

$dotenv = Dotenv::createImmutable($config['path']['HOME']);
$dotenv->load();

define('DB_DATABASE', $_ENV['DB_DATABASE']);
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USERNAME', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASS']);

$PlexLoader = new PlexLoader($config);

if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
    apache_setenv('dont-vary', '1');
}

if (!defined('APP_AUTHENTICATION')) {
    define('APP_AUTHENTICATION', false);
}

define('APP_HOME', $config['path']['APP_HOME']);
define('APP_HTML_ROOT', rtrim($_SERVER['CONTEXT_DOCUMENT_ROOT'], '/'));
define('APP_PATH', APP_HTML_ROOT.APP_HOME);

define('__THIS_FILE__', basename($_SERVER['SCRIPT_FILENAME']));
define('__THIS_PAGE__', basename($_SERVER['SCRIPT_FILENAME'], '.php'));



define('__PHP_ASSETS_DIR__', $_ENV['WEB_HOME'].'/assets');
define('__CONFIG_DIRECTORY__',__ROOT_DIRECTORY__.'/src/Config');
define('__PLEX_LIBRARY__', $_ENV['PLEX_HOME']);

define('__CACHE_DIR', __PLEX_LIBRARY__.'/.cache');
define('__ERROR_LOG_DIRECTORY__', APP_HTML_ROOT.'/logs');
define('__LAYOUT_PATH__', __PHP_ASSETS_DIR__.'/layouts');
define('__HTML_TEMPLATE__', __LAYOUT_PATH__.'/template/');

define('__URL_PATH__', APP_HOME);

define('__URL_HOME__', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].__URL_PATH__);

define('__LAYOUT_URL__', __URL_HOME__.'/assets/layouts/');

define('__MAX_PAGES_TO_SHOW__', 8);

define('ALLOWED_INACTIVITY_TIME', time() + 1 * 60);

define('__TEMPLATE_CONSTANTS__', [
    '__LAYOUT_URL__',
    '__URL_HOME__',
]);



$template       = new Template();

require_once __CONFIG_DIRECTORY__.'/paths.php';
require_once __CONFIG_DIRECTORY__.'/functions.php';
require_once __CONFIG_DIRECTORY__.'/enviroment.php';

$db       = new PlexSql(); // ('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$conn     = mysqli_connect('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$settings = new MetaSettings();

$val      = $settings->orderBy('type')->get();
if ($val) {
    foreach ($val as $u) {
        $setting[$u->name] = $u->type.';'.$u->value;

        if ('array' == $u->type) {
            define($u->name, json_decode($u->value, 1));

            if (defined('__DISPLAY_PAGES__') && array_key_exists(__THIS_FILE__, __DISPLAY_PAGES__)) {
                define('__SHOW_PAGES__', __DISPLAY_PAGES__[__THIS_FILE__]['pages']);
                define('__SHOW_SORT__', __DISPLAY_PAGES__[__THIS_FILE__]['sort']);

                if (__SHOW_PAGES__ == 0 && __SHOW_SORT__ == 0) {
                    define('__BOTTOM_NAV__', 0);
                } else {
                    define('__BOTTOM_NAV__', 1);
                }
            }
        } else {
            if (!defined($u->name)) {
                define($u->name, $u->value);
            }
        }
    }// end foreach

    define('__SETTINGS__', $setting);
}// end if

if (!defined('__BOTTOM_NAV__')) {
    define('__BOTTOM_NAV__', 0);
}

require_once __CONFIG_DIRECTORY__.'/variables.php';

define('__LAYOUT_HEADER__', __PHP_ASSETS_DIR__.'/Header.php');

define('__LAYOUT_NAVBAR__', __PHP_ASSETS_DIR__.'/Navbar.php');

define('__LAYOUT_FOOTER__', __PHP_ASSETS_DIR__.'/Footer.php');

logger('____________________________________________________________________________________________________________________');
define('__METADB_HASH', __CACHE_DIR.'/'.$cache_directory.'/metadb.hash');
