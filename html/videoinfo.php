<?php

use Plex\Core\Request;
use Plex\Template\Render;
use Plex\Modules\Database\FileListing;
use Plex\Template\Display\VideoDisplay;
define('TITLE', 'Home');
define('NONAVBAR', true);
define('VIDEOINFO', true);
define('SHOW_RATING', true);

require_once '_config.inc.php';

$videoInfo = (new FileListing(new Request))->getVideoDetails($_REQUEST['id']);
$vidInfo = (new VideoDisplay())->init('videoinfo')->Display($videoInfo);

Render::Display( $vidInfo);
