<?php
/**
 * plex web viewer
 */

use Camoo\Config\Config;
use Plex\EnvLoader;
use Tracy\Debugger;

session_start();

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

$config     = new Config(__ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'config.ini');

EnvLoader::LoadEnv($config['path']['mediatag'])->load();

if (!defined('APP_AUTHENTICATION')) {
    define('APP_AUTHENTICATION', false);
}
define('APP_HOME', $config['path']['APP_HOME']);
define('APP_HTML_ROOT', rtrim($_SERVER['CONTEXT_DOCUMENT_ROOT'], '/'));
define('APP_PATH', APP_HTML_ROOT.APP_HOME);

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

define('__ERROR_LOG_DIRECTORY__', APP_HTML_ROOT.'/logs');

define('Db_TABLE_VIDEO_FILE', Db_TABLE_PREFIX.'video_file');
define('Db_TABLE_VIDEO_INFO', Db_TABLE_PREFIX.'video_info');
define('Db_TABLE_VIDEO_CUSTOM', Db_TABLE_PREFIX.'video_custom');
define('Db_TABLE_VIDEO_TAGS', Db_TABLE_PREFIX.'video_metadata');

define('Db_TABLE_STUDIO', Db_TABLE_PREFIX.'studios');
define('Db_TABLE_GENRE', Db_TABLE_PREFIX.'genre');

define('Db_TABLE_ARTISTS', Db_TABLE_PREFIX.'artists');

define('Db_TABLE_SETTINGS', Db_TABLE_PREFIX.'settings');

define('Db_TABLE_PLAYLIST_VIDEOS', Db_TABLE_PREFIX.'playlist_videos');

define('Db_TABLE_PLAYLIST_DATA', Db_TABLE_PREFIX.'playlist_data');

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

define('SESSION_VARS',
    [
        'itemsPerPage' => '100',
        'library'      => 'Studios',
        'sort'         => 'f.added',
        'direction'    => 'DESC',
        // 'alpha' => '',
    ]);

// define('__SHOW_THUMBNAILS__', false);
require_once __ROOT_DIRECTORY__.'/src/Config/paths.php';

require_once __PHP_ASSETS_DIR__.'/header.inc.php';

require_once __PHP_ASSETS_DIR__.'/variables.php';
require_once __PHP_ASSETS_DIR__.'/settings.inc.php';

$const_keys = array_keys(get_defined_constants(true)['user']);
define('__TEMPLATE_CONSTANTS__', $const_keys);

logger('____________________________________________________________________________________________________________________');
define('__METADB_HASH', __CACHE_DIR.'/'.$cache_directory.'/metadb.hash');
