<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';

define('TITLE', 'Home');
define('USE_FILTER', true);
define('GRID_VIEW', true);

$fileinfo                = new FileListing($_REQUEST, $currentPage, $urlPattern);
[$results,$pageObj,$uri] = $fileinfo->getVideoArray();

$request_key             = uri_String($uri);

$redirect_string         = __THIS_FILE__.$request_key;

$referer_url             = '';
if ('home.php' != basename($_SERVER['HTTP_REFERER'])) {
    $referer_url = $_SERVER['HTTP_REFERER'];
}

$filelist_url            = 'files.php'.$request_key;
// define('BREADCRUMB', ['home' => "home.php", 'genre' => 'genre.php?allfiles=1', $genre => '',"file list"=>$filelist_url]);

require __LAYOUT_HEADER__;

$page_array              = [
    'total_files'     => $pageObj->totalRecords,
    'redirect_string' => $redirect_string,
];

//    echo display_filelist($results, '', $page_array);
$grid                    = new GridDisplay();
$table_body_html         = $grid->gridview($results, $pageObj->totalRecords);

template::echo('base/page', ['BODY' => $table_body_html]);

require __LAYOUT_FOOTER__;
