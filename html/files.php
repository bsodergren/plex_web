<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';

define('TITLE', 'Home');
define('USE_FILTER',true);
$fileinfo                = new FileListing($_REQUEST, $currentPage, $urlPattern);

[$results,$pageObj,$uri] = $fileinfo->getVideoArray();
logger('all files', $sql);
// $results = $db->query($sql);

dump(['uri',$uri]);
$request_key             = uri_String($uri);
$redirect_string         = __THIS_FILE__.$request_key;

$referer_url             = '';
if ('home.php' != basename($_SERVER['HTTP_REFERER'])) {
    $referer_url = $_SERVER['HTTP_REFERER'];
}
$gridview_url            = 'gridview.php'.$request_key;

// define('BREADCRUMB', ['home' => "home.php", $_REQUEST[$studio_key] => 'genre.php'.$request_key, $genre => '']);

require __LAYOUT_HEADER__;

$page_array              = [
    'total_files'     => $pageObj->totalRecords,
    'redirect_string' => $redirect_string,
];

$body                    = display_filelist($results, '', $page_array);

template::echo('base/page', ['BODY' => $body]);

require __LAYOUT_FOOTER__;
