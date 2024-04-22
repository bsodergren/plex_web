<?php

use Plex\Template\Render;
use UTMTemplate\HTML\Elements;

/*
 * plex web viewer
 */

define('__TAG_CAT_CLASS__', 'border border-2 border-dark  mx-2 d-flex');

require_once '_config.inc.php';

if (isset($_SESSION['sort'])) {
    $uri['sort'] = $_SESSION['sort'];
}

if (isset($_SESSION['direction'])) {
    $uri['direction'] = $_SESSION['direction'];
}

if (isset($_REQUEST['query'])) {
    $uri['query'] = $_REQUEST['query'];
}

if (isset($uri)) {
    $request_key = uri_String($uri);
}

$redirect_string = 'search.php'.$request_key;
$field           = 'genre';

// define('__TAG_CAT_CLASS__', ''); // border border-1 border-black');

$html = Render::html('pages/Tags/main',
    [
        'TAG_CAT_CLASS' => __TAG_CAT_CLASS__,
        // 'TAG_CLOUD_HTML' => Elements::keyword_cloud('genre'),
        //    'TAG_CLOUD_KEYWORD' => Elements::keyword_cloud('keyword'),
    ]);
Render::Display($html,'pages/Tags/body');
