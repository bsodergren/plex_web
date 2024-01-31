<?php
/**
 * plex web viewer
 */

use Nette\Utils\FileSystem;

require_once '_config.inc.php';
$carousel_js                        = '';
$video_buttons                      = '';

// dump(["req",$_REQUEST]);
if (array_key_exists('id', $_REQUEST)) {
    $id   = $_REQUEST['id'];
    // $cols = ['playlist_id'];
    // $db->where('playlist_video_id', $id);
} 

if (array_key_exists('playlist_id', $_REQUEST)) {
    $playlist_id   = $_REQUEST['playlist_id'];
    if (!array_key_exists('id', $_REQUEST))
    {
        $cols = ['playlist_id','playlist_video_id'];
        $db->where('playlist_id', $playlist_id);

        $playlist_result = $db->getOne(Db_TABLE_PLAYLIST_VIDEOS, null, $cols);
        $id          = $playlist_result['playlist_video_id'];
    }

}


// if (is_array($playlist_result)) {
//     if (array_key_exists('playlist_id', $playlist_result)) {
//         $playlist_id = $playlist_result['playlist_id'];
//         $playlist_video_id          = $playlist_result['playlist_video_id'];
//     }



//     dump($playlist_result);

// }


$cols                               = ['filename', 'fullpath','rating'];
$db->where('id', $id);
$result                             = $db->getone(Db_TABLE_VIDEO_FILE, null, $cols);

$active_title                       = null; // $result['title'];
if (null === $active_title) {
    $active_title = $result['filename'];
}


$fullpath                           = str_replace(__PLEX_LIBRARY__, APP_HOME.'/videos', $result['fullpath']);
$video_file                         = $fullpath.'/'.$result['filename'];
$playlist_height                    = '0';
$comments                           = '//';
if (isset($playlist_id)) {
    $video_js_params['PLAYLIST_HEIGHT'] = 50;
    $video_js_params['PLAYLIST_WIDTH']  = 20;
    

    $comments                           = '';
    $VideoDisplay                       = new VideoDisplay();

    $sql                                = 'select
        f.thumbnail,f.filename,m.title,p.playlist_video_id from
        '.Db_TABLE_VIDEO_FILE.' as f,
        '.Db_TABLE_PLAYLIST_VIDEOS.' as p,
        '.Db_TABLE_VIDEO_TAGS.' as m where (
            p.playlist_id = '.$playlist_id.' and
            p.playlist_video_id = f.id  and
            f.video_key = m.video_key);';
    $results                            = $db->query($sql);
    for ($i = 0; $i < count($results); ++$i) {
        $class = '';

        $title = $results[$i]['title'];
        if ('' == $results[$i]['title']) {
            $title = $results[$i]['filename'];
        }
        if ($id == $results[$i]['playlist_video_id']) {
            $class = ' active';
        }


        $carousel_item .= process_template(
            'video/carousel_item',
            [
                'PLAYLIST_ID'  => $playlist_id,

                'THUMBNAIL'    => $VideoDisplay->fileThumbnail($results[$i]['playlist_video_id'], 'alt="#" class="img-fluid" '),
                'CLASS_ACTIVE' => $class,
                'VIDEO_ID'     => $results[$i]['playlist_video_id'],
                'TITLE'        => $title,
            ]
        );
        if (' active' == $class) {
            $active_title = $title;
            $indx         = $i + 1;
            if (array_key_exists($indx, $results)) {
                $next_video_id = $results[$indx]['playlist_video_id'];
            } else {
                $next_video_id = $results[0]['playlist_video_id'];
            }

            $pndx         = $i - 1;
            if (array_key_exists($pndx, $results)) {
                $prev_video_id = $results[$pndx]['playlist_video_id'];
            } else {
                $prev_video_id = $results[0]['playlist_video_id'];
            }
        }
    }

    $carousel_js                        = process_template('video/carousel_js', ['PLAYLIST_ID' => $playlist_id]);
    $carousel                           = process_template('video/carousel', ['CAROUSEL_INNER_HTML' => $carousel_item]);
    $video_js_params['PLAYLIST_HEIGHT'] = 120;
    $video_js_params['PLAYLIST_WIDTH']  = 50;
    $video_js_params['PLAYLIST_ID']     = $playlist_id;

    $video_js_params['NEXT_VIDEO_ID']   = $next_video_id;
    $video_js_params['PREV_VIDEO_ID']   = $prev_video_id;

    $playlist_height                    = $video_js_params['PLAYLIST_HEIGHT'];
}
$video_js_params['COMMENT']         = $comments;
// $video_file                                       = FileSystem::unixSlashes(FileSystem::normalizePath($video_file));

$params                             = [
    'PAGE_TITLE'      => $result['title'],
    'STAR_RATING'     => $result['rating'],
    'VIDEO_ID'        => $id,
    'PLAYLIST_ID'     => $playlist_id,
    '__LAYOUT_URL__'  => __LAYOUT_URL__,
    'PLAYLIST_HEIGHT' => $playlist_height,
    'VIDEO_URL'       => $video_file,
    'VIDEO_TITLE'     => $active_title,
    'CAROUSEL_HTML'   => $carousel,
    'CAROUSEL_JS'     => $carousel_js,
    'VIDEO_JS'        => process_javascript('video/video_js', $video_js_params),
];
Template::echo('video/main', $params);
