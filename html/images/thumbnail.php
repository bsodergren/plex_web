<?php
/**
 * plex web viewer
 */

require_once '../_config.inc.php';

$id                 = $_GET['id'];

$query              = 'SELECT thumbnail FROM metatags_video_file WHERE id = '.$id;

$result             = $db->query($query);
// header('Content-Type: image/jpeg');
echo __URL_HOME__.$result[0]['thumbnail'];
