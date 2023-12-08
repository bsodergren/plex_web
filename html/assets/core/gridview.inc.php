<?php
/**
 * plex web viewer
 */

/**
 * Command like Metatag writer for video files.
 */
function gridview($results,$totalRecords)
{
    global $_SESSION,$_REQUEST;
    global $db,$sort_type_map;

    $total = count($results);
    if( $total  == 0){
        return "No results";
    }
    $sql = 'select * from '.Db_TABLE_PLAYLIST_DATA.';';
    $playlists = $db->query($sql);
    foreach($playlists as $p){
        $playlist_html .= process_template(
            'grid/playlist_link',
            [
                'PL_NAME'   => $p['name'],
                'PL_ID'      => $p['id'],
                'VIDEO_DATA'  => 'VIDEO_ID',
            ]
        );
        
    }
    $r               = 0;
   
    for ($i = 0; $i < count($results); ++$i) {
        $file_info = [
            'title'     => $results[$i]['title'],
            'library'    => $results[$i]['library'],
            'studio'    => $results[$i]['studio'],
            'substudio' => $results[$i]['substudio'],
            'artist'    => $results[$i]['artist'],
            'genre'     => $results[$i]['genre'],
            //'filename'  => $results[$i]['filename'],
            'duration' =>  videoDuration($results[$i]['duration']),
            
            // 'Duration' => videoDuration($results[$i]['duration']),
        ];
        if ('' != $results[$i]['substudio']) {
            $substudio = $results[$i]['substudio'];
        }
        if ('' != $results[$i]['studio']) {
            $studio = $results[$i]['studio'];
        }
        $videoInfo = '';
        foreach ($file_info as $field => $value) {
            // if ($value != '') {
            $videoInfo .= process_template(
                'grid/video_data',
                [
                    'FILE_FIELD' => ucfirst($field),
                    'FILE_INFO'  => $value,
                ]
            );
            // }
        }

        $cell_html .= process_template(
            'grid/cell',
            [
                'PLAYLIST_LINKS' => str_replace('VIDEO_ID',$results[$i]['id'],  $playlist_html ),
                'THUMBNAIL'   => $results[$i]['thumbnail'],
                'ROW_ID'      => $results[$i]['id'],
                'VIDEO_DATA'  => $videoInfo,
                'ROWNUM'     => $results[$i]['rownum'],
                'ROW_TOTAL' => $totalRecords ,

            ]
        );
    }

    $row_html        = process_template('grid/row', ['ROW_CELLS' => $cell_html]);

    $table_body_html = process_template('grid/table', [
        'HIDDEN_STUDIO_NAME' => add_hidden('studio', $studio).add_hidden('substudio', $substudio),
        'ROWS_HTML'          => $row_html,
        'INFO_NAME'          => $sort_type_map['map'][$_REQUEST['sort']],
    ]);

    return $table_body_html;
}
