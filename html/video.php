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
$fullpath                           = str_replace(__PLEX_LIBRARY__, APP_HOME.'/videos', $result['fullpath']);


$video_file                         = $fullpath.'/'.$result['filename'];
$video_js_params['PLAYLIST_HEIGHT'] = 50;
$video_js_params['PLAYLIST_WIDTH']  = 20;

if (isset($playlist_id)) {
    $sql                                = 'select f.thumbnail,f.filename,f.title,p.playlist_videos from '.Db_TABLE_FILEDB.' as f, '.Db_TABLE_PLAYLIST_VIDEOS.' as p where (p.playlist_id = '.$playlist_id.' and p.playlist_videos = f.id);';
    $results                            = $db->query($sql);

    for ($i = 0; $i < count($results); ++$i) {
        $class = '';

        $title =  $results[$i]['title'];
        if($results[$i]['title'] == "") {
            $title =  $results[$i]['filename'];
        }
        if ($id == $results[$i]['playlist_videos']) {
            $class = ' active';
        }
        $carousel_item .= process_template(
            'video/carousel_item',
            [
                'THUMBNAIL'    => $results[$i]['thumbnail'],
                'CLASS_ACTIVE' => $class,
                'VIDEO_ID'     => $results[$i]['playlist_videos'],
                'TITLE'  => $title,
            ]
        );
        if($class == ' active'){
            $active_title = $title;
            $indx = $i + 1;
            if (key_exists($indx, $results)) {
                $next_video_id =  $results[$indx]['playlist_videos'];
            } else {
                $next_video_id =  $results[0]['playlist_videos'];
            }

            $pndx = $i - 1;
            if (key_exists($pndx, $results)) {
                $prev_video_id =  $results[$pndx]['playlist_videos'];
            } else {
                $prev_video_id =  $results[0]['playlist_videos'];
            }


        }
    }

    $carousel_js                        = process_template('video/carousel_js', []);
    $carousel                           = process_template('video/carousel', ['CAROUSEL_INNER_HTML' => $carousel_item]);
    $video_js_params['PLAYLIST_HEIGHT'] = 145;
    $video_js_params['PLAYLIST_WIDTH']  = 50;
    $video_js_params['NEXT_VIDEO_ID']  = $next_video_id;
    $video_js_params['PREV_VIDEO_ID']  = $prev_video_id;
}

$params                             = [
 'PAGE_TITLE'     => $result['title'],
 'VIDEO_ID'       => $id,
 '__LAYOUT_URL__' => __LAYOUT_URL__,
 'VIDEO_URL'      => $video_file,
 'VIDEO_TITLE'    => $active_title,
 'CAROUSEL_HTML'  => $carousel,
 'CAROUSEL_JS'    => $carousel_js,
 'VIDEO_JS'       => process_template('video/video_js', $video_js_params),
];

echo process_template('video/main', $params);
