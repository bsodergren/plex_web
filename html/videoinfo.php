<?php

use Plex\Modules\Database\VideoDb;
use Plex\Modules\Display\VideoDisplay;
use Plex\Template\Render;

require_once '_config.inc.php';

$videoInfo = (new VideoDb())->getVideoDetails($_REQUEST['id']);
$vidInfo   = (new VideoDisplay())->init('videoinfo')->Display($videoInfo);

Render::Display($vidInfo);
