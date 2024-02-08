<?php
/**
 * plex web viewer
 */


 
 use Plex\Template\Render;
 use Plex\Core\FileListing;
 use Plex\Core\ProcessForms;
 use Plex\Template\Template;
 use Plex\Template\Layout\Footer;
 use Plex\Template\Layout\Header;
 use Plex\Template\Display\Display;
 use Plex\Template\Display\VideoDisplay;
 
define('TITLE', 'Home');
define('NONAVBAR', true);
define('VIDEOINFO', true);
define('SHOW_RATING', true);

require_once '_config.inc.php';

$id                        = $_REQUEST['id'];

$fileinfo                  = new FileListing();
$videoInfo                 = $fileinfo->getVideoDetails($id);

// die(print_r(THEME_SWITCHER));

$vidInfo                   = (new VideoDisplay())->init('videoinfo');
$vidInfo->showVideoDetails = true;
$body                      = $vidInfo->Display($videoInfo);
// dump($body);
//$body['THEME_SWITCHER']    = THEME_SWITCHER;

// Template::echo('videoinfo/videoinfo', ['BODY' => $body, 'DELETE_HTML' => $delete_html]);
Template::echo('videoinfo/page', $body);
