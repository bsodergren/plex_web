<?php
session_start();

if (!defined('APP_AUTHENTICATION')) DEFINE("APP_AUTHENTICATION", TRUE);
if (!defined('APP_HOME')) DEFINE("APP_HOME", "/plex_web");
if (!defined('APP_PATH')) DEFINE("APP_PATH", $_SERVER['DOCUMENT_ROOT'] . APP_HOME);

define('__THIS_FILE__',basename($_SERVER['SCRIPT_FILENAME']));

define('__THIS_PAGE__',basename($_SERVER['SCRIPT_FILENAME'],".php"));

require_once("/home/bjorn/mysql_pwd.php");
DEFINE("__SQL_DB__", "pornhub_2");

if (!defined('DB_DATABASE')) define('DB_DATABASE', __SQL_DB__);
if (!defined('DB_HOST')) define('DB_HOST', '127.0.0.1');
if (!defined('DB_USERNAME')) define('DB_USERNAME', __SQL_USER__);
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', __SQL_PASSWD__);
if (!defined('DB_PORT')) define('DB_PORT', '');

define('Db_TABLE_PREFIX', "metatags" . "_");
DEFINE("__PHP_ASSETS_DIR__", APP_PATH . "/assets");
define('__PHP_INC_CLASS_DIR__', __PHP_ASSETS_DIR__ . '/class');
define('__PHP_INC_CORE_DIR__', __PHP_ASSETS_DIR__ . '/core');

define('__COMPOSER_LIB__', __PHP_ASSETS_DIR__ . '/lib/vendor');
set_include_path(get_include_path() . PATH_SEPARATOR . __COMPOSER_LIB__);
require_once __COMPOSER_LIB__ . '/autoload.php';



define('__PLEX_LIBRARY__', '/home/bjorn/plex/XXX');
define('__CACHE_DIR', __PLEX_LIBRARY__ . "/.cache");







define('Db_TABLE_FILEDB', Db_TABLE_PREFIX . 'filedb');


define('Db_TABLE_STUDIO', Db_TABLE_PREFIX . 'studios');
define('Db_TABLE_GENRE', Db_TABLE_PREFIX . 'genre');


define('Db_TABLE_ARTISTS', Db_TABLE_PREFIX . 'artists');

define('Db_TABLE_SETTINGS', Db_TABLE_PREFIX . 'settings');


define('Db_TABLE_FILEINFO', Db_TABLE_PREFIX . 'fileinfo');
define('Db_TABLE_PLAYLIST',  'playlist_videos');
define('Db_TABLE_PLAYLIST_INFO',  'playlist_data');



define('__LAYOUT_PATH__', __PHP_ASSETS_DIR__ . '/layouts');


define('__HTML_TEMPLATE__', __LAYOUT_PATH__ . '/template/');


define('__PHP_TEMPLATE__', __LAYOUT_PATH__ . '/php_template/');



define('__LAYOUT_HEADER__', __LAYOUT_PATH__ . '/header.php');


define('__LAYOUT_NAVBAR__', __LAYOUT_PATH__ . '/navbar.php');


define('__LAYOUT_FOOTER__', __LAYOUT_PATH__ . '/footer.php');


define('__URL_PATH__', APP_HOME);


define('__URL_HOME__', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . __URL_PATH__);


define('__LAYOUT_URL__', __URL_HOME__ . '/assets/layouts/');

define('__MAX_PAGES_TO_SHOW__', 8);


require_once __PHP_ASSETS_DIR__ . "/header.inc.php";
require_once __PHP_ASSETS_DIR__ . '/settings.inc.php';

require_once __PHP_ASSETS_DIR__ . '/variables.php';
define('__METADB_HASH', __CACHE_DIR . "/" . $cache_directory . "/metadb.hash");
