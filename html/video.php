<?php

use Plex\Core\VideoPlayer;
use Plex\Template\Render;

require_once '_config.inc.php';
define('SHOW_RATING', true);

$videoPlayer = new VideoPlayer();


$videoPlayer->videoInfo();

if (isset($videoPlayer->playlist_id))
{
    $videoPlayer->getPlaylist();
}

$videoPlayer->getVideo();

Render::echo('video/main', $videoPlayer->params);
