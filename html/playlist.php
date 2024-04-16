<?php

use Plex\Modules\Playlist\Playlist;
use Plex\Modules\Process\Forms;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;
use UTMTemplate\HTML\Elements;

/**
 * plex web viewer.
 */

require_once '_config.inc.php';
$playlist_id = null;
$playlist_links = '';
if (isset($_REQUEST['playlist_id'])) {
    $p = new Forms($_REQUEST);
    $p->process();

    $playlist_id = $_REQUEST['playlist_id'];
}
$cell_html = '';
$table_body_html = '';
$main_links = '';
$playlist = new Playlist();
if (null === $playlist_id) {
    $results = $playlist->showPlaylists();
    // UtmDump($results);
    $total = count($results);
    for ($i = 0; $i < count($results); ++$i) {
        $playlist_image = '';
        $library = $results[$i]['library'];

        if (0 == $i) {
            $prev = $library;
        }
        $preview = $playlist->showPlaylistPreview($results[$i]['playlist_id']);
        foreach ($preview as $r => $row) {
            $playlist_image .= Render::html('playlist/thumbnail/image', ['image' => __URL_HOME__.$row['thumbnail']]);
            // UtmDump($row);
        }

        $params = [
            'PLAYLIST_ID' => $results[$i]['playlist_id'],
            'PLAYLIST_NAME' => $results[$i]['name'],
            'PLAYLIST_COUNT' => $results[$i]['count'],
            'ThumbnailPreview' => Render::html('playlist/thumbnail/thumbnail', ['PlaylistPreviewImage' => $playlist_image]),
        ];
        if ($library == $prev) {
            $playlist_links .= Render::html('playlist/playlist_link', $params);
        } else {
            $table_body_html .= Render::html('playlist/main', [
                'PLAYLIST_LIST' => $playlist_links,
                'PLAYLIST_LIBRARY' => $prev,
            ]);
            $playlist_links = Render::html('playlist/playlist_link', $params);

            $prev = $library;
        }
    }

    $table_body_html .= Render::html('playlist/main', [
        'PLAYLIST_LIST' => $playlist_links,
        'PLAYLIST_LIBRARY' => $library,
    ]);
} else {
    $VideoDisplay = new Functions();

    $results = $playlist->getPlaylist($playlist_id);
    $list = $playlist->showPlaylists(true);
    $playlist_LinkArray['All'] = __URL_HOME__.'/playlist.php';

    foreach ($list as $l => $plRow) {
        $plCanvas_image = '';
        $plCanvas_id = $plRow['playlist_id'];
        if ($playlist_id == $plCanvas_id) {
            continue;
        }
        $plCanvas_name = $plRow['name'];
        $plCanvast_count = $plRow['count'];
        $playlist_LinkArray[$plCanvas_name] = __URL_HOME__.'/playlist.php?playlist_id='.$plCanvas_id.'|'.$plRow['count'];

        // $preview = $playlist->showPlaylistPreview($plRow['playlist_id']);
        // foreach ($preview as $r => $row) {
        //     $plCanvas_image = Render::html('playlist/canvas/image', ['image' => __URL_HOME__.$row['thumbnail']]);
        // }

        // $plCanvast_count_params = [
        //     'PLAYLIST_ID' => $plCanvas_id,
        //     'PLAYLIST_NAME' => $plCanvas_name,
        //     'PLAYLIST_COUNT' => $plCanvast_count,
        //     'PlaylistPreviewImage' => $plCanvas_image,
        // ];

        // $canvas_html .= Render::html('playlist/canvas/item', $plCanvast_count_params);
    }

    // $Playlist_Canvas = Render::html('playlist/canvas/block', ['CANVAS_LIST' => $canvas_html]);

    $total = count($results);

    for ($i = 0; $i < count($results); ++$i) {
        $thumbnail = '';
        if (OptionIsTrue(SHOW_THUMBNAILS)) {
            $thumbnail = Render::html(
                'playlist/thumbnail',
                [
                    'THUMBNAIL' => $VideoDisplay->fileThumbnail($results[$i]['id'], 'alt="#" class="img-fluid" '),
                    'VIDEO_ID' => $results[$i]['id'],

                    'PLAYLIST_VIDEO_ID' => $results[$i]['playlist_video_id'],
                ]
            );
        }

        $cell_html .= Render::html(
            'playlist/cell',
            [
                // 'VID_NUMBER' => $i +1,
                'TITLE' => $results[$i]['title'],
                'THUMBNAIL' => $thumbnail,
                'VIDEO_ID' => $results[$i]['id'],
                'PLAYLIST_ID' => $playlist_id,
                'PLAYLIST_VIDEO_ID' => $results[$i]['playlist_video_id'],
            ]
        );
    }

    $form_url = __URL_HOME__.'/playlist.php?playlist_id='.$playlist_id.'';
    $form_action = Elements::add_hidden('playlist_id', $playlist_id);

    $table_body_html = Render::html('playlist/table', [
        'FORM_URL' => $form_url,
        'HIDDEN' => $form_action,
        'PLAYLIST_ID' => $playlist_id,
        'PLAYLIST_VIDEOS' => $total,
        'PLAYLIST_GENRE' => $results[0]['genre'],
        'PLAYLIST_NAME' => $results[0]['name'],
        'CELLS_HTML' => $cell_html,
        // 'Playlist_Canvas' => $Playlist_Canvas,
    ]);
    define('PLAYLIST_DROPDOWN', $playlist_LinkArray);
}

Render::Display($table_body_html);
