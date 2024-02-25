<?php

use Plex\Template\Render;
use Plex\Modules\Video\Player;

require_once '_config.inc.php';
define('SHOW_RATING', true);

$videoPlayer = new Player();
$videoPlayer->PlayVideo();

//Render::echo($videoPlayer->getPlayerTemplate(), $videoPlayer->params);
