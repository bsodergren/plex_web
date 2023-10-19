<?php
/**
 * plex web viewer
 */

use Camoo\Config\Config;
use Dotenv\Dotenv;

session_start();

// HOME=D:\\development\\plex_web
// APP_PATH D:\\development\\plex_web
define('__ROOT_DIRECTORY__', dirname(realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']), 1));

define('__COMPOSER_LIB__', __ROOT_DIRECTORY__.'/vendor');
set_include_path(get_include_path().\PATH_SEPARATOR.__COMPOSER_LIB__);

require_once __COMPOSER_LIB__.'/autoload.php';

$config    = new Config(__ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'config.ini');

$dotenv    = Dotenv::createImmutable($config['path']['mediatag']);
$dotenv->load();

if (!defined('APP_AUTHENTICATION')) {
    define('APP_AUTHENTICATION', false);
}
define('APP_HOME', $config['path']['APP_HOME']);
define('APP_PATH', $_SERVER['CONTEXT_DOCUMENT_ROOT'].APP_HOME);

define('__THIS_FILE__', basename($_SERVER['SCRIPT_FILENAME']));
define('__THIS_PAGE__', basename($_SERVER['SCRIPT_FILENAME'], '.php'));

define('DB_DATABASE', $_ENV['DB_DATABASE']);

define('DB_HOST', $_ENV['DB_HOST']);

define('DB_USERNAME', $_ENV['DB_USER']);

define('DB_PASSWORD', $_ENV['DB_PASS']);

define('Db_TABLE_PREFIX', 'metatags_');
define('__PHP_ASSETS_DIR__', $_ENV['WEB_HOME'].'/assets');
define('__PHP_INC_CLASS_DIR__', __PHP_ASSETS_DIR__.'/class');
define('__PHP_INC_CORE_DIR__', __PHP_ASSETS_DIR__.'/core');
define('__PHP_INC_INCLUDE_DIR__', __PHP_ASSETS_DIR__.'/includes');

define('__PLEX_LIBRARY__', $_ENV['PLEX_HOME']);
define('__CACHE_DIR', __PLEX_LIBRARY__.'/.cache');

define('__ERROR_LOG_DIRECTORY__', APP_PATH.'/logs');

define('Db_TABLE_FILEDB', Db_TABLE_PREFIX.'filedb');

define('Db_TABLE_STUDIO', Db_TABLE_PREFIX.'studios');
define('Db_TABLE_GENRE', Db_TABLE_PREFIX.'genre');

define('Db_TABLE_ARTISTS', Db_TABLE_PREFIX.'artists');

define('Db_TABLE_SETTINGS', Db_TABLE_PREFIX.'settings');

define('Db_TABLE_FILEINFO', Db_TABLE_PREFIX.'fileinfo');

define('Db_TABLE_PLAYLIST_VIDEOS', 'playlist_videos');

define('Db_TABLE_PLAYLIST_DATA', 'playlist_data');

define('__LAYOUT_PATH__', __PHP_ASSETS_DIR__.'/layouts');

define('__HTML_TEMPLATE__', __LAYOUT_PATH__.'/template/');

define('__PHP_TEMPLATE__', __LAYOUT_PATH__.'/php_template/');

define('__LAYOUT_HEADER__', __LAYOUT_PATH__.'/header.php');

define('__LAYOUT_NAVBAR__', __LAYOUT_PATH__.'/navbar.php');

define('__LAYOUT_FOOTER__', __LAYOUT_PATH__.'/footer.php');

define('__URL_PATH__', APP_HOME);

define('__URL_HOME__', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].__URL_PATH__);

define('__LAYOUT_URL__', __URL_HOME__.'/assets/layouts/');

define('__MAX_PAGES_TO_SHOW__', 8);

define('ALLOWED_INACTIVITY_TIME', time() + 1 * 60);

define('__TEMPLATE_CONSTANTS__', [
    '__LAYOUT_URL__',
    '__URL_HOME__',
]);

require_once __PHP_ASSETS_DIR__.'/header.inc.php';

require_once __PHP_ASSETS_DIR__.'/settings.inc.php';

require_once __PHP_ASSETS_DIR__.'/variables.php';
define('__METADB_HASH', __CACHE_DIR.'/'.$cache_directory.'/metadb.hash');
