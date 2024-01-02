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

$vidInfo                   = new VideoDisplay('videoinfo');
$vidInfo->showVideoDetails = true;
$body                      = $vidInfo->filelist($videoInfo);


// template::echo('videoinfo/videoinfo', ['BODY' => $body, 'DELETE_HTML' => $delete_html]);
template::echo('videoinfo/videoinfo', $body);
require __LAYOUT_FOOTER__;
