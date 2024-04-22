<?php

use Plex\Core\Request;
use Plex\Modules\Database\FileListing;
use Plex\Modules\Display\Display;
use Plex\Modules\Display\VideoDisplay;
use Plex\Template\Render;

require_once '_config.inc.php';

$fileinfo                = new FileListing(new Request());
[$results,$pageObj,$uri] = $fileinfo->getFavorites();

$style = 'List';
if(isset($_REQUEST['style']) == 'Grid') {
    Display::$CrubURL['list'] = __THIS_FILE__.'?style=List';
    $style = 'Grid';
} else {
    Display::$CrubURL['grid'] = __THIS_FILE__.'?style=Grid';
}

$vidInfo = (new VideoDisplay($style))->init();
$body    = $vidInfo->Display($results, [
    'total_files'     => $pageObj->totalRecords,
   // 'redirect_string' => $redirect_string,
]);
