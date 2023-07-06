<?php
/**
 * Command like Metatag writer for video files.
 */

require_once '_config.inc.php';
$carousel_js                        = '';

if(key_exists('id',$_REQUEST)) {
    $id                                 = $_REQUEST['id'];
    $cols                               = ['playlist_id'];
    $db->where('playlist_videos', $id);
}

if(key_exists('playlist_id',$_REQUEST)) {
    $id                                 = $_REQUEST['playlist_id'];
    $cols                               = ['playlist_id'];
    $db->where('playlist_id', $id);  
}


$playlist_result                    = $db->getOne(Db_TABLE_PLAYLIST_VIDEOS, null, $cols);
if (is_array($playlist_result)) {
    if (array_key_exists('playlist_id', $playlist_result)) {
        $playlist_id = $playlist_result['playlist_id'];
        $id = $playlist_result['playlist_videos'];
    }
}

$cols                               = ['filename', 'fullpath', 'title'];
$db->where('id', $id);
$result                             = $db->getone(Db_TABLE_FILEDB, null, $cols);

$title                              = $result['title'];
$fullpath                           = str_replace('/home/bjorn/plex/XXX', '/videos', $result['fullpath']);
$video_file                         = $fullpath.'/'.$result['filename'];
$video_js_params['PLAYLIST_HEIGHT'] = 50;
$video_js_params['PLAYLIST_WIDTH']  = 20;

if (isset($playlist_id)) {
    $sql                                = 'select f.thumbnail,f.filename,p.playlist_videos from '.Db_TABLE_FILEDB.' as f, '.Db_TABLE_PLAYLIST_VIDEOS.' as p where (p.playlist_id = '.$playlist_id.' and p.playlist_videos = f.id);';
    $results                            = $db->query($sql);

    for ($i = 0; $i < count($results); ++$i) {
        $class = 'carousel-item ';

        if ($id == $results[$i]['playlist_videos']) {
            $class = 'carousel-item active';
        }
        $carousel_item .= process_template(
            'video/carousel_item',
            [
                'THUMBNAIL'    => $results[$i]['thumbnail'],
                'CLASS_ACTIVE' => $class,
                'VIDEO_ID'     => $results[$i]['playlist_videos'],
            ]
        );
    }

    $carousel_js                        = process_template('video/carousel_js', []);
    $carousel                           = process_template('video/carousel', ['CAROUSEL_INNER_HTML' => $carousel_item]);
    $video_js_params['PLAYLIST_HEIGHT'] = 145;
    $video_js_params['PLAYLIST_WIDTH']  = 50;
}

$params                             = [
 'PAGE_TITLE'     => $result['title'],
 'VIDEO_ID'       => $id,
 '__LAYOUT_URL__' => __LAYOUT_URL__,
 'VIDEO_URL'      => $video_file,
 'VIDEO_TITLE'    => $title,
 'CAROUSEL_HTML'  => $carousel,
 'CAROUSEL_JS'    => $carousel_js,
 'VIDEO_JS'       => process_template('video/video_js', $video_js_params),
];

echo process_template('video/main', $params);
