<?php
require_once '_config.inc.php';

define('TITLE', 'Home');

require __LAYOUT_HEADER__;
$playlist_id = 0;

$sql = "select f.thumbnail from metatags_filedb as f, playlists as p where (p.playlist_id = ".$playlist_id ." and p.playlist_videos = f.id);";
$results = $db->query($sql);


for ($i = 0; $i < count($results); $i++) {
    $cell_html .= process_template(
        "grid/cell",
        [
            'THUMBNAIL' => $results[$i]['thumbnail'],
            'ROW_ID' =>  $results[$i]['id'],
        ]
    );
}

$row_html =  process_template("grid/row", ['ROW_CELLS' => $cell_html]);

$table_body_html = process_template("grid/table", ['ROWS_HTML' =>  $row_html]);

echo process_template("grid/main", ['BODY_HTML' =>  $table_body_html]);


require __LAYOUT_FOOTER__;
