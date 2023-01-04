<?php

define('__THIS_FILE__', basename($_SERVER['PHP_SELF']));
define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));

require_once __PHP_ASSETS_DIR__.'/defines.php';

require_once __PHP_ASSETS_DIR__.'/settings.inc.php';

set_include_path(get_include_path().PATH_SEPARATOR.__COMPOSER_LIB__);
require_once __COMPOSER_LIB__.'/autoload.php';

use Tracy\Debugger;

Debugger::enable(Debugger::DEVELOPMENT );
Debugger::$dumpTheme    = 'dark';
Debugger::$showLocation = (Tracy\Dumper::LOCATION_CLASS | Tracy\Dumper::LOCATION_LINK);
Debugger::$showBar = 1;


if (!defined('__ERROR_LOG_DIR__')) {
    define('__ERROR_LOG_DIR__', APP_PATH.'/'.php_sapi_name().'_logs');
}


$logfile_name = 'plexweb.log';
if (__HTML_POPUP__ == true) {
    $logfile_name = 'plexweb.html.log';
}

if (!defined('__ERROR_FILE_NAME__')) {
    define('__ERROR_FILE_NAME__', $logfile_name);
}

if (!defined('ERROR_LOG_FILE')) {
    define('ERROR_LOG_FILE', __ERROR_LOG_DIR__.'/'.__ERROR_FILE_NAME__);
}


$all = opendir(__PHP_INC_CORE_DIR__);
while ($file = readdir($all)) {
    if (!is_dir(__PHP_INC_CORE_DIR__.'/'.$file)) {
        if (preg_match('/\.(php)$/', $file)) {
            $f    = fopen(__PHP_INC_CORE_DIR__.'/'.$file, 'r');
            $line = fgets($f);
            fclose($f);
            if (strpos($line, '#skip') == false) {
                include_once __PHP_INC_CORE_DIR__.'/'.$file;
            }
        }
    }
}


$colors        = new Colors();
$model_display = new display();

if (__SCRIPT_NAME__ != 'logs') {
    if (is_file(ERROR_LOG_FILE)) {
        $lines = count(file(ERROR_LOG_FILE));
        if ($lines > 1000) {
            unlink(ERROR_LOG_FILE);
        }
    }
}

if (__SCRIPT_NAME__ != 'logs') {
    logger('start for '.__SCRIPT_NAME__);
}

if (!isset($_SESSION['library'])) {
    $_SESSION['library'] = 'Studio';
}

if (isset($_REQUEST['library'])) {
    $_SESSION['library'] = $_REQUEST['library'];
}

$in_directory = $_SESSION['library'];
$cache_directory = $_SESSION['library'];



if ($in_directory == 'Studio') {
    $in_directory = 'Studios';
}

/*if ($in_directory == 'Home Videos') {
    $in_directory = 'HomeVideos';
}
*/
define('__METADB_HASH', __CACHE_DIR . "/" . $cache_directory . "/metadb.hash");


$request_key   = '';


if (!isset($_SESSION['sort'])) {
    $_SESSION['sort'] = 'title';
}

if (isset($_REQUEST['sort'])) {
    $_SESSION['sort'] = $_REQUEST['sort'];
}


$query_string = '&'.urlQuerystring($_SERVER['QUERY_STRING']);


if (!isset($_SESSION['direction'])) {
    $_SESSION['direction'] = 'ASC';
}

if (isset($_REQUEST['direction'])) {
    if ($_REQUEST['direction'] == 'ASC') {
         $_SESSION['direction'] = 'DESC';
    }

    if ($_REQUEST['direction'] == 'DESC') {
         $_SESSION['direction'] = 'ASC';
    }
}

$url_array = [
    'url'          => $_SERVER['SCRIPT_NAME'],
    'query_string' => $query_string,
    'current'      => $_SESSION['sort'],
    'direction'    => $_SESSION['direction'],
    'sort_types'   => [
        'Studio'   => 'studio',
        'Artist'   => 'artist',
        'Title'    => 'title',
        'Filename' => 'filename',
        'Duration' => 'Duration',
    ],
];
