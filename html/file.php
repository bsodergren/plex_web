<?php

use Plex\Core\Request;
use Plex\Template\Render;

use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
use Plex\Template\Display\Display;
use Plex\Modules\Database\FileListing;
use Plex\Modules\Database\FolderListing;
use Plex\Template\Display\Layout\FolderDisplay;
use Plex\Template\Display\VideoDisplay;

require_once '_config.inc.php';

define('TITLE', 'Home');
define('__SHOW_SORT__', true);

define('ALPHA_SORT', true);
define('SHOW_RATING', true);

$folderList = new FolderListing(new Request);

$folders = $folderList -> getCurrentFolderList();
$files = $folderList -> getCurrentFileList();

//[$results,$pageObj,$uri] = $folderList->getVideoArray();


if(count($files) > 0 ) {
    foreach($files as $filename){
        $vInfo = $folderList->getVideoDetails($filename);
        $results[] = $vInfo[0];
       
    }
}

$vidInfo = (new VideoDisplay('Folder'))->init('fileBrowser');
$vidInfo->parentfolder = $folderList->parent;
$vidInfo->folderCounts = $folderList->scan(FM_PATH);
$body = $vidInfo->Display($folders,$results, []);


 $request_key = uri_String($uri);
 $redirect_string = __THIS_FILE__.$request_key;
// if (array_key_exists('genre', $_REQUEST)) {
//     $studio_url = urlQuerystring($redirect_string, 'genre');
// }

// $referer_url = '';

// if ('home.php' != basename($_SERVER['HTTP_REFERER'])) {
//     $referer_url = $_SERVER['HTTP_REFERER'];
// }
// Display::$CrubURL['grid'] = 'grid.php';

// $vidInfo = (new VideoDisplay('List'))->init('filelist');
// $body = $vidInfo->Display($results, [
//     'total_files' => $pageObj->totalRecords,
//     'redirect_string' => $redirect_string,
// ]);


// Header::Display();
Render::Display($body);
// Footer::Display();
