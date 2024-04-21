<?php

use Plex\Modules\VideoJs\Player;
use Plex\Template\Render;
use UTMTemplate\Template;

require_once '_config.inc.php';

$videoPlayer = new Player();

// Template::$params = $videoPlayer->params;
Render::echo($videoPlayer->getPlayerTemplate('main'));
