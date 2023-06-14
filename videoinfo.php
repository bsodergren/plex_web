<?php
/**
 * Command like Metatag writer for video files.
 */

define('TITLE', 'Home');
define('NONAVBAR', true);
define('VIDEOINFO', true);

require_once '_config.inc.php';

$id        = $_REQUEST['id'];
/*
$cols = array("v.filename","v.thumbnail","v.title","v.artist","v.genre","v.studio","v.substudio","v.keyword","v.added","v.fullpath","v.duration"
,"i.filesize","i.format","i.bit_rate","i.width","i.height");

$db->join(Db_TABLE_FILEINFO ." i", "v.video_key=i.video_key", "LEFT");
$db->where("v.id",  $id);
$videoInfo = $db->get(Db_TABLE_FILEDB." v", null, $cols);
*/

$cols      = ['id', 'filename', 'video_key', 'thumbnail', 'title', 'artist', 'genre', 'studio', 'substudio', 'keyword', 'added', 'fullpath', 'duration'];
$db->where('id', $id);
$videoInfo = $db->get(Db_TABLE_FILEDB, null, $cols);

require __LAYOUT_HEADER__;

$body      = display_filelist($videoInfo);

template::echo('filelist/videoinfo', ['BODY' => $body]);
require __LAYOUT_FOOTER__;
