<?php

use Plex\Core\Request;
use Plex\Template\Render;
use Plex\Modules\Database\FileListing;
use Plex\Modules\Display\Display;
use Plex\Modules\Display\VideoDisplay;

/**
 * plex web viewer.
 */

require_once '_config.inc.php';

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

Display::$CrubURL['list'] = 'list.php'; // .$request_key;

//    echo display_filelist($results, '', $page_array);
$grid = (new VideoDisplay('Grid'))->init();

$table_body_html = $grid->Display($results, [
    'total_files' => $pageObj->totalRecords,
    'redirect_string' => $redirect_string,
]);
Render::Display($table_body_html);
