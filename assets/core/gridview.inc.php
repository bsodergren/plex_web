<?php


function gridview($results)
{

    global $_SESSION;

    $r = 0;

    for ($i = 0; $i < count($results); $i++) {

        $file_info = [
            'title' => $results[$i]['title'],
            'studio' => $results[$i]['studio'],
            'substudio' => $results[$i]['substudio'],
            'artist' => $results[$i]['artist'],

            'genre' => $results[$i]['genre'],

            // 'Duration' => videoDuration($results[$i]['duration']),

        ];

        $videoInfo = '';
        foreach ($file_info as $field => $value) {
            //if ($value != '') {
            $videoInfo .= process_template(
                "grid/video_data",
                [
                    'FILE_FIELD' => ucfirst($field),
                    'FILE_INFO'  => $value,
                ]
            );
            // }

        }


        $cell_html .= process_template(
            "grid/cell",
            [
                'THUMBNAIL' => $results[$i]['thumbnail'],
                'ROW_ID' =>  $results[$i]['id'],
                'VIDEO_DATA'  => $videoInfo,
            ]
        );
    }

    $row_html =  process_template("grid/row", ['ROW_CELLS' => $cell_html]);

    $table_body_html = process_template("grid/table", [
        'ROWS_HTML' =>  $row_html,
        'INFO_NAME' => $_SESSION['sort'],
    ]);

    return $table_body_html;
}
