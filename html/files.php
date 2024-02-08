<?php
/**
 * plex web viewer
 */

use Plex\Core\FileListing;
use Plex\Template\Template;
use Plex\Template\Display\Display;
use Plex\Template\Display\VideoDisplay;

require_once '_config.inc.php';

define('TITLE', 'Home');
define('__SHOW_SORT__', true);

define('ALPHA_SORT', true);
define('SHOW_RATING', true);

Template::$Render         = true;

$fileinfo                 = new FileListing($_REQUEST, $currentPage, $urlPattern);
[$results,$pageObj,$uri]  = $fileinfo->getVideoArray();

$request_key              = uri_String($uri);
$redirect_string          = __THIS_FILE__.$request_key;

if (array_key_exists('genre', $_REQUEST)) {
    $studio_url = urlQuerystring($redirect_string, 'genre');
    //  $studio_url  = 'studio.php?studio='.$_REQUEST['studio'];
}

// $res = count($results);
// if($res == 0){
//     $redirect_string = urlQuerystring($redirect_string, 'alpha');
//   //  echo JavaRefresh($redirect_string, 0);
//  //   exit;
// }

$referer_url              = '';
if ('home.php' != basename($_SERVER['HTTP_REFERER'])) {
    $referer_url = $_SERVER['HTTP_REFERER'];
}
Display::$CrubURL['grid'] = 'gridview.php';

$vidInfo                  = (new VideoDisplay('List'))->init('filelist');
$body                     = $vidInfo->Display($results, [
    'total_files'     => $pageObj->totalRecords,
    'redirect_string' => $redirect_string,
]);
Template::echo('filelist/page', $body);

Template::render();
