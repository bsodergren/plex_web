<?php
/**
 * plex web viewer
 */

define('TITLE', 'Home');
define('NONAVBAR', true);
define('VIDEOINFO', true);

require_once '_config.inc.php';

$id                        = $_REQUEST['id'];

$fileinfo                  = new FileListing();
$videoInfo                 = $fileinfo->getVideoDetails($id);

 \Plex\Template\Layout\Header::Display();
// die(print_r(THEME_SWITCHER));
$vidInfo                   = new VideoCard('VideoCard');
$vidInfo->showVideoDetails = true;
$body                      = $vidInfo->filelist($videoInfo);
$body['THEME_SWITCHER']    = THEME_SWITCHER;

// Template::echo('videoinfo/videoinfo', ['BODY' => $body, 'DELETE_HTML' => $delete_html]);
Template::echo('VideoCard/main', $body);
 \Plex\Template\Layout\Footer::Display();
