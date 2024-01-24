<?php
/**
 * plex web viewer
 */

use Nette\Utils\FileSystem;

require_once '_config.inc.php';

if (array_key_exists('id', $_REQUEST)) {
    $id   = $_REQUEST['id'];
    $cols = ['playlist_id'];
    $db->where('playlist_video_id', $id);
}

if (array_key_exists('playlist_id', $_REQUEST)) {
    $id   = $_REQUEST['playlist_id'];
    $cols = ['playlist_id'];
    $db->where('playlist_id', $id);
}

$playlist_result                    = $db->getOne(Db_TABLE_PLAYLIST_VIDEOS, null, $cols);
// dump($playlist_result);
if (is_array($playlist_result)) {
    if (array_key_exists('playlist_id', $playlist_result)) {
        $playlist_id = $playlist_result['playlist_id'];
        $id          = $playlist_result['playlist_video_id'];
    }
}



$cols              = ['filename', 'fullpath'];
$db->where('id', $id);
$result            = $db->getone(Db_TABLE_VIDEO_FILE, null, $cols);

$fullpath          = str_replace(__PLEX_LIBRARY__, APP_HOME.'/videos', $result['fullpath']);

$video_file        = $fullpath.'/'.$result['filename'];
$carousel_item[] = VideoPlayer::playlistVideo($video_file,$result['filename'],'');
if (isset($playlist_id)) {
    $comments                           = '';
    $VideoDisplay                       = new VideoDisplay();

    $sql                                = 'select
        f.thumbnail,f.filename, f.fullpath,m.title,p.playlist_video_id from
        '.Db_TABLE_VIDEO_FILE.' as f,
        '.Db_TABLE_PLAYLIST_VIDEOS.' as p,
        '.Db_TABLE_VIDEO_TAGS.' as m where (
            p.playlist_id = '.$playlist_id.' and
            p.playlist_video_id = f.id  and
            f.video_key = m.video_key);';
    $results                            = $db->query($sql);

    for ($i = 0; $i < count($results); ++$i) {
        
        $class = '';

        $fullpath          = str_replace(__PLEX_LIBRARY__, APP_HOME.'/videos', $results[$i]['fullpath']);

        $pl_video_file        = $fullpath.'/'.$results[$i]['filename'];

        $carousel_item[] = VideoPlayer::playlistVideo($pl_video_file,$results[$i]['title'],$VideoDisplay->fileThumbnail($results[$i]['playlist_video_id']));
    }

        
}
// $video_file                                       = FileSystem::unixSlashes(FileSystem::normalizePath($video_file));
    $playlist_script = implode(",",$carousel_item);

$params            = [
    'VIDEO_ID'       => $id,
    '__LAYOUT_URL__' => __LAYOUT_URL__,
    'VIDEO_URL'      => $video_file,
    'PLAYLIST' => $playlist_script,
];

$pl = new VideoPlayer('videojs-playlist');
$pl->javascript('videojs-playlist');

$plu = new VideoPlayer('videojs-playlist-ui');
$plu->javascript('videojs-playlist-ui');
$plu->stylesheet('videojs-playlist-ui');

$js = new VideoPlayer('video.js');
$js->javascript('video');
$js->stylesheet('video-js');

$seek = new VideoPlayer('videojs-seek-buttons');
$seek->javascript('videojs-seek-buttons',1);
$seek->stylesheet('videojs-seek-buttons');

$params['SCRIPTS'] .= $js->render();
$params['SCRIPTS'] .= $pl->render();
 $params['SCRIPTS'] .= $plu->render();

// $params['SCRIPTS'] .= $seek->render();
Template::echo('testVideo/main', $params);
