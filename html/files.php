<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';

define('TITLE', 'Home');
define('USE_FILTER', true);
Template::$Render        = true;
//  dd($_REQUEST);
$fileinfo                = new FileListing($_REQUEST, $currentPage, $urlPattern);

[$results,$pageObj,$uri] = $fileinfo->getVideoArray();

logger('all files', $sql);
// $results = $db->query($sql);
//  dd($results);
$request_key             = uri_String($uri);
$redirect_string         = __THIS_FILE__.$request_key;
if(array_key_exists('genre',$_REQUEST))
{
    $studio_url = urlQuerystring($redirect_string, 'genre');
  //  $studio_url  = 'studio.php?studio='.$_REQUEST['studio'];

}
$res = count($results);
if($res == 0){
    $redirect_string = urlQuerystring($redirect_string, 'alpha');
  //  echo JavaRefresh($redirect_string, 0);
 //   exit;
}

$referer_url             = '';
if ('home.php' != basename($_SERVER['HTTP_REFERER'])) {
    $referer_url = $_SERVER['HTTP_REFERER'];
}
Render::$CrubURL['grid']            = 'gridview.php';//.$request_key;

// define('BREADCRUMB', ['home' => "home.php", $_REQUEST[$studio_key] => 'genre.php'.$request_key, $genre => '']);

// require __LAYOUT_HEADER__;

$page_array              = [
    'total_files'     => $pageObj->totalRecords,
    'redirect_string' => $redirect_string,
];




// dd($page_array);
$vidInfo                 = new VideoDisplay('filelist');
$body                    = $vidInfo->filelist($results, '', $page_array);
$body['PLAYLIST_ADD_BUTTON'] = Render::displayPlaylistButton();
$body['PLAYLIST_ADD_ALL_BUTTON'] = Render::displayPlaylistAddAllButton();

template::echo('filelist/page', $body);
// template::echo('filelist/page', ['BODY' => $body]);

// require __LAYOUT_FOOTER__;

Template::render();
