<?php


function gridview($results)
{

    global $_SESSION;

    $r = 0;

    for ($i = 0; $i < count($results); $i++) {

        $file_info = match ($_SESSION['sort']) {
            'genre' => $results[$i]['genre'],
            'Duration' => videoDuration($results[$i]['duration']),
            'duration' => videoDuration($results[$i]['duration']),
            'studio' => $results[$i]['studio'],
            'artist' => $results[$i]['artist'],
            'title' => $results[$i]['title'],
            'added' => $results[$i]['added'],
            'filename' => $results[$i]['filename'],
        };


        $cell_html .= process_template(
            "grid/cell",
            [
                'THUMBNAIL' => $results[$i]['thumbnail'],
                'ROW_ID' =>  $results[$i]['id'],
                'FILE_INFO'  => $file_info,
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
