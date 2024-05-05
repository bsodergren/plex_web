<?php

ob_start();

use Camoo\Config\Config;
use Plex\Core\Request;
use Plex\Core\RoboLoader;
use Plex\EnvLoader;
use Tracy\Debugger;
use UTMTemplate\Template;
use UTMTemplate\UtmDevice;

// error_reporting(\E_ALL & ~\E_NOTICE & ~\E_WARNING);

session_start();
if (!array_key_exists('library', $_REQUEST)) {
    $_REQUEST['library'] = $_SESSION['library'];
}

define('__ROOT_DIRECTORY__', dirname(realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']), 1));
define('__PLEX_APP_DIR__', __ROOT_DIRECTORY__.'/src');

define('__PHP_CONFIG_DIR__', __PLEX_APP_DIR__.'/Config');
define('__PHP_YAML_DIR__', __PHP_CONFIG_DIR__.'/Routes');
define('__COMPOSER_LIB__', __ROOT_DIRECTORY__.'/vendor');
set_include_path(get_include_path().\PATH_SEPARATOR.__COMPOSER_LIB__);

require_once __COMPOSER_LIB__.'/autoload.php';
// Debugger::enable();
Debugger::enable(Debugger::Development);

$config = new Config(__ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'config.ini');
dd($config);

EnvLoader::LoadEnv($config['path']['mediatag'])->load();

Request::startPage();
foreach ($config['constants'] as $name => $value) {
    define($name, $value);
}

require_once __PHP_CONFIG_DIR__.'/Language.php';
require_once __PHP_CONFIG_DIR__.'/paths.php';
require_once __PHP_CONFIG_DIR__.'/urlpaths.php';

require_once __PHP_CONFIG_DIR__.'/database.php';
require_once __PHP_CONFIG_DIR__.'/Functions.php';

Template::$registeredCallbacks = [
    '\Plex\Template\Callbacks\FunctionCallback::FUNCTION_CALLBACK' => 'callback_parse_function',
    '\Plex\Template\Callbacks\FunctionCallback::SCRIPTINCLUDE_CALLBACK' => 'callback_script_include'];

// Template::$registeredFilters = [
//     '\Plex\Template\Callbacks\URLFilter::parse_urllink' => ['a=href' => ['library' => $_REQUEST['library']]],
// ];

Template::$USER_TEMPLATE_DIR = __HTML_TEMPLATE__;
Template::$TEMPLATE_COMMENTS = true;
Template::$SITE_URL = __LAYOUT_URL__;
Template::$SITE_PATH = __LAYOUT_PATH__;
Template::$ASSETS_URL = __LAYOUT_URL__.\DIRECTORY_SEPARATOR.'Default';
Template::$ASSETS_PATH = __LAYOUT_PATH__.\DIRECTORY_SEPARATOR.'Default';
Template::$CACHE_DIR = __TPL_CACHE_DIR__;
Template::$USE_TEMPLATE_CACHE = false;
UtmDevice::$DETECT_BROWSER = true;
UtmDevice::$USER_DEFAULT_TEMPLATE = __HTML_TEMPLATE__;
UtmDevice::$USER_MOBILE_TEMPLATE = __MOBILE_TEMPLATE__;
UtmDevice::$MOBILE_ASSETS_URL = __LAYOUT_URL__.\DIRECTORY_SEPARATOR.'Mobile';
UtmDevice::$MOBILE_ASSETS_PATH = __LAYOUT_PATH__.\DIRECTORY_SEPARATOR.'Mobile';

$device = new UtmDevice();
RoboLoader::loadPage();
$const_keys = array_keys(get_defined_constants(true)['user']);
define('__TEMPLATE_CONSTANTS__', $const_keys);
logger('____________________________________________________________________________________________________________________');
