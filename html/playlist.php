<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';

$process         = new ProcessForms($_REQUEST);

if (isset($_REQUEST['playlist_id'])) {
    $playlist_id = $_REQUEST['playlist_id'];
}
$table_body_html = '';
$main_links      = '';

if (null === $playlist_id) {
    $sql     = 'select count(p.playlist_video_id) as count, p.playlist_id, d.name,
    d.library from '.Db_TABLE_PLAYLIST_DATA.' as d, '.Db_TABLE_PLAYLIST_VIDEOS.' as p where (p.playlist_id = d.id) group by p.playlist_id ORDER BY library ASC;';
    // dd($sql);
    $results = $db->query($sql);
    $total   = count($results);
    for ($i = 0; $i < count($results); ++$i) {
        $library = $results[$i]['library'];

        if (0 == $i) {
            $prev = $library;
        }

        $params  = [
            'PLAYLIST_ID'    => $results[$i]['playlist_id'],
            'PLAYLIST_NAME'  => $results[$i]['name'],
            'PLAYLIST_COUNT' => $results[$i]['count'],
        ];
        if ($library == $prev) {
            $playlist_links .= process_template('playlist/playlist_link', $params);
        } else {
            $table_body_html .= process_template('playlist/main', [
                'PLAYLIST_LIST'    => $playlist_links,
                'PLAYLIST_LIBRARY' => $prev,
            ]);
            $playlist_links = process_template('playlist/playlist_link', $params);

            $prev           = $library;
        }
    }

    $table_body_html .= process_template('playlist/main', [
        'PLAYLIST_LIST'    => $playlist_links,
        'PLAYLIST_LIBRARY' => $library,
    ]);
} else {
    $VideoDisplay    = new VideoDisplay();

    $sql             = 'select f.thumbnail,f.id,d.name,d.genre,p.id as playlist_video_id,m.title from  '.Db_TABLE_PLAYLIST_DATA.' as d,
     '.Db_TABLE_VIDEO_FILE.' as f, '.Db_TABLE_PLAYLIST_VIDEOS.' as p, '.Db_TABLE_VIDEO_TAGS.' as m
      where (p.playlist_id = '.$playlist_id.' and p.playlist_video_id = f.id and d.id = p.playlist_id and f.video_key = m.video_key);';
    $results         = $db->query($sql);
    $total           = count($results);

    for ($i = 0; $i < count($results); ++$i) {
        $thumbnail = '';
        if (__SHOW_THUMBNAILS__ == true) {
            $thumbnail = process_template(
               'playlist/thumbnail',
                [
                    'THUMBNAIL'         => $VideoDisplay->fileThumbnail($results[$i]['id'], 'alt="#" class="img-fluid" '),
                    'VIDEO_ID'          => $results[$i]['id'],
                 
                    'PLAYLIST_VIDEO_ID' => $results[$i]['playlist_video_id'],
                ]
            );
        }

        $cell_html .= process_template(
            'playlist/cell',
            [
                // 'VID_NUMBER' => $i +1,
                'TITLE'             => $results[$i]['title'],
                'THUMBNAIL'         => $thumbnail,
                'VIDEO_ID'          => $results[$i]['id'],
                'PLAYLIST_ID' =>  $playlist_id ,
                'PLAYLIST_VIDEO_ID' => $results[$i]['playlist_video_id'],
            ]
        );
    }

    $form_url        = __URL_HOME__.'/playlist.php?playlist_id='.$playlist_id.'';
    $form_action     = add_hidden('playlist_id', $playlist_id);

    $table_body_html = process_template('playlist/table', [
        'FORM_URL'        => $form_url,
        'HIDDEN'          => $form_action,
        'PLAYLIST_ID'     => $playlist_id,
        'PLAYLIST_VIDEOS' => $total,
        'PLAYLIST_GENRE'  => $results[0]['genre'],
        'PLAYLIST_NAME'   => $results[0]['name'],
        'CELLS_HTML'      => $cell_html,
    ]);
}

define('TITLE', 'Home');
define('GRID_VIEW', 1);

require __LAYOUT_HEADER__;
template::echo('base/page', ['BODY' => $table_body_html]);

require __LAYOUT_FOOTER__;
