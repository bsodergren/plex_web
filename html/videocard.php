<?php
/**
 * plex web viewer
 */

use Plex\Core\FileListing;
use Plex\Template\Display\VideoCard;
use Plex\Template\Template;

/*
 * plex web viewer
 */

define('TITLE', 'Home');
define('NONAVBAR', true);
define('VIDEOINFO', true);

require_once '_config.inc.php';

$id                        = 32945;

$fileinfo                  = new FileListing();
$videoInfo                 = $fileinfo->getVideoDetails($id);

\Plex\Template\Layout\Header::Display();
// die(print_r(THEME_SWITCHER));
$vidInfo                   = new VideoCard('VideoCard');
$vidInfo->showVideoDetails = true;
$body                      = $vidInfo->filelist($videoInfo);

// Template::echo('videoinfo/videoinfo', ['BODY' => $body, 'DELETE_HTML' => $delete_html]);
Template::echo('base/page', $body);
\Plex\Template\Layout\Footer::Display();
