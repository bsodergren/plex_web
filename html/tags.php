<?php

use Plex\Template\HTML\Elements;
/**
 * plex web viewer
 */


 use Plex\Template\Render;
 use Plex\Core\FileListing;
 use Plex\Core\ProcessForms;
 use Plex\Template\Template;
 use Plex\Template\Layout\Footer;
 use Plex\Template\Layout\Header;
 use Plex\Template\Display\Display;
 use Plex\Template\Display\VideoDisplay;
 use Plex\Core\PlexSql;

define('__TAG_CAT_CLASS__', 'border border-2 border-dark  mx-2 d-flex');

require_once '_config.inc.php';

define('TITLE', 'tags');

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


define('__TAG_CAT_CLASS__', ''); // border border-1 border-black');

$html            = Render::html('cloud/main',
    [
        'TAG_CAT_CLASS'  => __TAG_CAT_CLASS__,
        'TAG_CLOUD_HTML' => Elements::keyword_cloud('genre'),
        //   'TAG_CLOUD_KEYWORD' => Elements::keyword_cloud('keyword'),
    ]);

Template::echo('base/page', ['BODY' => $html]);

