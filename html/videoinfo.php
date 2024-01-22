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

require __LAYOUT_HEADER__;
// die(print_r(THEME_SWITCHER));

$vidInfo                   = new VideoDisplay('videoinfo');
$vidInfo->showVideoDetails = true;
$body                      = $vidInfo->filelist($videoInfo);
$body['THEME_SWITCHER']    = THEME_SWITCHER;

// template::echo('videoinfo/videoinfo', ['BODY' => $body, 'DELETE_HTML' => $delete_html]);
template::echo('videoinfo/videoinfo', $body);
require __LAYOUT_FOOTER__;
