<?php
/**
 * Command like Metatag writer for video files.
 */
define('__TAG_CAT_CLASS__','border border-1 border-dark mx-2 ');

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
    $request_key   = uri_String($uri);
}

$redirect_string = 'search.php'.$request_key;
$field           = 'genre';



include_once __LAYOUT_HEADER__;

define('__TAG_CAT_CLASS__','border border-1 border-black');

$html = process_template('cloud/main', 
[
    'TAG_CAT_CLASS' => __TAG_CAT_CLASS__,
    'TAG_CLOUD_HTML' => keyword_cloud('genre'),
    'TAG_CLOUD_KEYWORD' => keyword_cloud('keyword')
]);
Template::echo('base/page', ['BODY' => $html ]);

include_once __LAYOUT_FOOTER__;
