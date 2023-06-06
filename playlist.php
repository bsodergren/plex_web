<?php
require_once '_config.inc.php';

define('TITLE', 'Home');

require __LAYOUT_HEADER__;

if (isset($_REQUEST['playlist_id'])) {
    $playlist_id = $_REQUEST['playlist_id'];
}
$table_body_html = '';
$main_links = '';


if ($playlist_id === null) {
    $sql = "select count(p.playlist_videos) as count, p.playlist_id, d.name from " . Db_TABLE_PLAYLIST_INFO . " as d, " . Db_TABLE_PLAYLIST . " as p where (p.playlist_id = d.id) group by p.playlist_id;";
} else {
    $sql = "select f.thumbnail,f.id,d.name from  " . Db_TABLE_PLAYLIST_INFO . " as d, " . Db_TABLE_FILEDB . " as f, " . Db_TABLE_PLAYLIST . " as p where (p.playlist_id = " . $playlist_id . " and p.playlist_videos = f.id and d.id = p.playlist_id);";
}
$results = $db->query($sql);

if ($playlist_id === null) {
    for ($i = 0; $i < count($results); $i++) {

        $table_body_html .= $studio . "<a href='playlist.php?playlist_id=" . $results[$i]['playlist_id'] . "'>" . $results[$i]['name'] . "</a> " . $results[$i]['count'] . " videos <br>";
    }
} else {

    for ($i = 0; $i < count($results); $i++) {
        $cell_html .= process_template(
            "playlist/cell",
            [
                'THUMBNAIL' => $results[$i]['thumbnail'],
                'ROW_ID' =>  $results[$i]['id'],
            ]
        );
    }

    $row_html =  process_template("playlist/row", ['ROW_CELLS' => $cell_html]);

    $table_body_html = process_template("playlist/table", [
        'PLAYLIST_NAME' =>  $results[0]['name'],
        'ROWS_HTML' =>  $row_html
    ]);
}


template::echo("base/page", ['BODY' => $table_body_html]);
require __LAYOUT_FOOTER__;
