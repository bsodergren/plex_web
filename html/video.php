<?php

use Plex\Modules\Video\Player;
use Plex\Template\Render;
use UTMTemplate\Template;

require_once '_config.inc.php';

$videoPlayer = new Player();

// if (isset($videoPlayer->playlist_id)) {
//     utmdump('has Playlist ID '.$videoPlayer->playlist_id);
//     $videoPlayer->getPlaylist();
// }

$videoPlayer->PlayVideo();

Template::$params = $videoPlayer->params;
Render::echo($videoPlayer->getPlayerTemplate('main'));
