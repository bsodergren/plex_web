<?php

use Camoo\Config\Config;
use Plex\EnvLoader;
use Tracy\Debugger;

session_start();

define('__ROOT_DIRECTORY__', dirname(realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']), 1));
define('__PLEX_APP_DIR__', __ROOT_DIRECTORY__.'/src');
define('__PHP_CONFIG_DIR__', __PLEX_APP_DIR__.'/Config');
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

//require_once __PHP_CONFIG_DIR__.'/MyDumper.php';
$config = new Config(__ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'config.ini');

EnvLoader::LoadEnv($config['path']['mediatag'])->load();

(new \UTM\Utm);

require_once __PHP_CONFIG_DIR__.'/Language.php';
require_once __PHP_CONFIG_DIR__.'/paths.php';
require_once __PHP_CONFIG_DIR__.'/urlpaths.php';

require_once __PHP_CONFIG_DIR__.'/database.php';
require_once __PHP_CONFIG_DIR__.'/constants.php';
require_once __PHP_CONFIG_DIR__.'/Functions.php';

require_once __PHP_CONFIG_DIR__.'/header.inc.php';
require_once __PHP_CONFIG_DIR__.'/variables.php';
require_once __PHP_CONFIG_DIR__.'/navbar.php';

require_once __PHP_CONFIG_DIR__.'/settings.inc.php';
$const_keys = array_keys(get_defined_constants(true)['user']);
define('__TEMPLATE_CONSTANTS__', $const_keys);
logger('____________________________________________________________________________________________________________________');
