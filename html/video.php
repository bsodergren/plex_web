<?php

use Plex\Core\VideoPlayer;
use Plex\Template\Render;

require_once '_config.inc.php';
define('SHOW_RATING', true);

$videoPlayer = new VideoPlayer();
$videoPlayer->videoTemplate = "videoPlyr";
//$videoPlayer->videoTemplate = "video";

$videoPlayer->videoInfo();

if (isset($videoPlayer->playlist_id))
{
    $videoPlayer->getPlaylist();
}

$videoPlayer->getVideo();

Render::echo($videoPlayer->videoTemplate.'/main', $videoPlayer->params);
