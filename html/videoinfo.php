<?php

use Plex\Core\Request;
use Plex\Template\Render;
use Plex\Modules\Database\FileListing;
use Plex\Modules\Database\VideoDb;
use Plex\Modules\Display\VideoDisplay;

require_once '_config.inc.php';

$videoInfo = (new VideoDb)->getVideoDetails($_REQUEST['id']);
$vidInfo = (new VideoDisplay())->init('videoinfo')->Display($videoInfo);

Render::Display( $vidInfo);
