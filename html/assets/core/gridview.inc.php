<?php
/**
 * Command like Metatag writer for video files.
 */

function gridview($results)
{
    global $_SESSION;

    $r               = 0;

    for ($i = 0; $i < count($results); ++$i) {
        $file_info = [
            'title'     => $results[$i]['title'],
            'studio'    => $results[$i]['studio'],
            'substudio' => $results[$i]['substudio'],
            'artist'    => $results[$i]['artist'],

            'genre'     => $results[$i]['genre'],

            // 'Duration' => videoDuration($results[$i]['duration']),
        ];
        if($results[$i]['substudio'] != '') {
            $substudio = $results[$i]['substudio'];
        }
        if($results[$i]['studio'] != '') {
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
                'THUMBNAIL'   => __URL_HOME__.$results[$i]['thumbnail'],
                'ROW_ID'      => $results[$i]['id'],
                'VIDEO_DATA'  => $videoInfo,
            ]
        );
    }

    $row_html        =  process_template('grid/row', ['ROW_CELLS' => $cell_html]);

    $table_body_html = process_template('grid/table', [
        'HIDDEN_STUDIO_NAME' => add_hidden("studio", $studio) . add_hidden('substudio',$substudio),
        'ROWS_HTML' => $row_html,
        'INFO_NAME' => $_SESSION['sort'],
    ]);

    return $table_body_html;
}
