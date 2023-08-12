<?php
/**
 * Command like Metatag writer for video files.
 */
require_once '_config.inc.php';

if (isset($_REQUEST['playlist_id'])) {
    $playlist_id = $_REQUEST['playlist_id'];
}

if (array_key_exists('action', $_REQUEST)) {
    if (isset($_REQUEST['action'])) {
        $action = $_REQUEST['action'];
    }

    if ($action == 'delete') {
        $sql = 'delete d,v from '.Db_TABLE_PLAYLIST_DATA.'  d join '.Db_TABLE_PLAYLIST_VIDEOS.' v on d.id = v.playlist_id where d.id = '.$playlist_id.'';
        $results = $db->query($sql);
        echo myHeader('playlist.php', 0);
    }

    if ($action == 'save') {
        if (isset($_REQUEST['playlist_name'])) {
            $playlist_name = $_REQUEST['playlist_name'];
            if ($playlist_name != '') {
                $update[] = " name = '".$playlist_name."' ";
            }
        }

        if (isset($_REQUEST['playlist_genre'])) {
            $playlist_genre = $_REQUEST['playlist_genre'];
            if ($playlist_genre != '') {
                $update[] = " genre = '".$playlist_genre."' ";
            }
        }

        if (isset($update)) {
            $update_str = implode(', ', $update);
            $sql = 'UPDATE '.Db_TABLE_PLAYLIST_DATA.' SET '.$update_str.' WHERE id = '.$playlist_id.'';
            $results = $db->query($sql);
        }

        if (isset($_REQUEST['prune_playlist'])) {
            $video_ids = $_REQUEST['prune_playlist'];
            foreach ($video_ids as $_ => $id) {
                $video_id_array[] = $id;
            }
            $video_ids_str = implode(', ', $video_id_array);
            $sql = 'delete from '.Db_TABLE_PLAYLIST_VIDEOS.' where id in ('.$video_ids_str.')';
            $results = $db->query($sql);
        }

        $form_url = 'playlist.php?playlist_id='.$playlist_id.'';
        echo  myHeader($form_url);
    }
} else {
    $table_body_html = '';
    $main_links = '';

    if (null === $playlist_id) {
        $sql = 'select count(p.playlist_videos) as count, p.playlist_id, d.name, d.library from '.Db_TABLE_PLAYLIST_DATA.' as d, '.Db_TABLE_PLAYLIST_VIDEOS.' as p where (p.playlist_id = d.id) group by p.playlist_id ORDER BY library ASC;';
        //dd($sql);
        $results = $db->query($sql);
        for ($i = 0; $i < count($results); $i++) {
            $library = $results[$i]['library'];

            if ($i == 0) {
                $prev = $library;
            }

            $params = [
                'PLAYLIST_ID'    => $results[$i]['playlist_id'],
                'PLAYLIST_NAME'  => $results[$i]['name'],
                'PLAYLIST_COUNT' => $results[$i]['count'],
            ];
            if ($library == $prev) {
                $playlist_links .= process_template('playlist/playlist_link', $params);
            } else {
                $table_body_html .= process_template('playlist/main', [
                    'PLAYLIST_LIST' => $playlist_links,
                    'PLAYLIST_LIBRARY'        => $prev,
                ]);
                $playlist_links = process_template('playlist/playlist_link', $params);

                $prev = $library;
            }
        }

        $table_body_html .= process_template('playlist/main', [
            'PLAYLIST_LIST' => $playlist_links,
            'PLAYLIST_LIBRARY'        => $library,
        ]);
        
    } else {
        $sql = 'select f.thumbnail,f.id,d.name,d.genre,p.id as playlist_video_id from  '.Db_TABLE_PLAYLIST_DATA.' as d, '.Db_TABLE_FILEDB.' as f, '.Db_TABLE_PLAYLIST_VIDEOS.' as p where (p.playlist_id = '.$playlist_id.' and p.playlist_videos = f.id and d.id = p.playlist_id);';
        $results = $db->query($sql);

        for ($i = 0; $i < count($results); $i++) {
            $cell_html .= process_template(
                'playlist/cell',
                [
                    'THUMBNAIL' => $results[$i]['thumbnail'],
                    'VIDEO_ID'  => $results[$i]['id'],
                    'PLAYLIST_VIDEO_ID'  => $results[$i]['playlist_video_id'],
                ]
            );
        }

        $form_url = 'playlist.php?playlist_id='.$playlist_id.'';
        $form_action = add_hidden('playlist_id', $playlist_id);

        $table_body_html = process_template('playlist/table', [
            'FORM_URL' => $form_url,
            'HIDDEN' => $form_action,
            'PLAYLIST_ID' => $playlist_id,
            'PLAYLIST_GENRE' => $results[0]['genre'],
            'PLAYLIST_NAME' => $results[0]['name'],
            'CELLS_HTML'    => $cell_html,
        ]);
    }
}

define('TITLE', 'Home');
define('GRID_VIEW', 1);
require __LAYOUT_HEADER__;
template::echo('base/page', ['BODY' => $table_body_html]);
require __LAYOUT_FOOTER__;
