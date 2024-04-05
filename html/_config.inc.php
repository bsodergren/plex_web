<?php

use Camoo\Config\Config;
use Plex\Core\RoboLoader;
use Plex\EnvLoader;
use UTMTemplate\Template;
use Tracy\Debugger;
// session_abort();
// session_destroy();

session_start();
define('__ROOT_DIRECTORY__', dirname(realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']), 1));
define('__PLEX_APP_DIR__', __ROOT_DIRECTORY__.'/src');
define('__PHP_CONFIG_DIR__', __PLEX_APP_DIR__.'/Config');
define('__PHP_YAML_DIR__', __PHP_CONFIG_DIR__.'/Routes');
define('__COMPOSER_LIB__', __ROOT_DIRECTORY__.'/vendor');

set_include_path(get_include_path().\PATH_SEPARATOR.__COMPOSER_LIB__);
require_once __COMPOSER_LIB__.'/autoload.php';
//Debugger::enable(Debugger::Development);

// require_once __PHP_CONFIG_DIR__.'/MyDumper.php';
$config = new Config(__ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'config.ini');

EnvLoader::LoadEnv($config['path']['mediatag'])->load();

foreach ($config['constants'] as $name => $value) {
    define($name, $value);
}
// new \UTM\Utm();

require_once __PHP_CONFIG_DIR__.'/Language.php';
require_once __PHP_CONFIG_DIR__.'/paths.php';
require_once __PHP_CONFIG_DIR__.'/urlpaths.php';

require_once __PHP_CONFIG_DIR__.'/database.php';
require_once __PHP_CONFIG_DIR__.'/Functions.php';

Template::$registeredCallbacks = [
    '\Plex\Template\Callbacks\FunctionCallback::FUNCTION_CALLBACK'      => 'callback_parse_function',
    '\Plex\Template\Callbacks\FunctionCallback::SCRIPTINCLUDE_CALLBACK' => 'callback_script_include'];

Template::$USER_TEMPLATE_DIR = __HTML_TEMPLATE__;
Template::$TEMPLATE_COMMENTS = true;
Template::$SITE_URL          = __LAYOUT_URL__;
Template::$SITE_PATH         = __LAYOUT_PATH__;
RoboLoader::loadPage();

$const_keys = array_keys(get_defined_constants(true)['user']);
define('__TEMPLATE_CONSTANTS__', $const_keys);
logger('____________________________________________________________________________________________________________________');
