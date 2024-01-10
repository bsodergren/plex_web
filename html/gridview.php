<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';

define('TITLE', 'Home');
define('USE_FILTER', true);
define('GRID_VIEW', true);
define('__SHOW_SORT__',true);

define('ALPHA_SORT', true);
$fileinfo                = new FileListing($_REQUEST, $currentPage, $urlPattern);
[$results,$pageObj,$uri] = $fileinfo->getVideoArray();

$request_key             = uri_String($uri);

$redirect_string         = __THIS_FILE__.$request_key;

$referer_url             = '';
if ('home.php' != basename($_SERVER['HTTP_REFERER'])) {
    $referer_url = $_SERVER['HTTP_REFERER'];
}

Render::$CrubURL['list'] = 'files.php'; // .$request_key;
$res                     = count($results);
if (0 == $res) {
    $redirect_string = urlQuerystring($redirect_string, 'alpha');
    echo JavaRefresh($redirect_string, 0);
    exit;
}

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
