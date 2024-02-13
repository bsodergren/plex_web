<?php

use Plex\Core\Request;
use Plex\Template\Render;
use Plex\Core\FileListing;
use Plex\Template\Display\Display;
use Plex\Template\Display\VideoDisplay;

/**
 * plex web viewer.
 */

require_once '_config.inc.php';

define('TITLE', 'Home');
define('GRID_VIEW', true);
define('__SHOW_SORT__', true);
define('SHOW_RATING', true);

define('ALPHA_SORT', true);
$fileinfo = new FileListing(new Request);
[$results,$pageObj,$uri] = $fileinfo->getVideoArray();

$request_key = uri_String($uri);

$redirect_string = __THIS_FILE__.$request_key;

$referer_url = '';
if ('home.php' != basename($_SERVER['HTTP_REFERER'])) {
    $referer_url = $_SERVER['HTTP_REFERER'];
}

$res = count($results);
if (0 == $res) {
    $redirect_string = urlQuerystring($redirect_string, 'alpha');
    echo Elements::javaRefresh($redirect_string, 0);
    exit;
}

Display::$CrubURL['list'] = 'files.php'; // .$request_key;

//    echo display_filelist($results, '', $page_array);
$grid = (new VideoDisplay('Grid'))->init();

$table_body_html = $grid->Display($results, [
    'total_files' => $pageObj->totalRecords,
    'redirect_string' => $redirect_string,
]);
Render::Display($table_body_html);
