<?php
require_once '_config.inc.php';

define('TITLE', 'Home');

require __LAYOUT_HEADER__;

if (isset($_REQUEST['playlist_id'])) {
    $playlist_id = $_REQUEST['playlist_id'];
}
$table_body_html = '';
$main_links = '';


if($playlist_id === null){
    $sql = "select p.playlist_id from metatags_filedb as f, playlists as p where (p.playlist_videos = f.id) group by p.playlist_id;";
} else {
    $sql = "select f.thumbnail,f.id from metatags_filedb as f, playlists as p where (p.playlist_id = ".$playlist_id ." and p.playlist_videos = f.id);";
}

$results = $db->query($sql);
if($playlist_id === null){
    for ($i = 0; $i < count($results); $i++) {

        $main_links .= $studio."<a href='playlist.php?playlist_id=". $results[$i]['playlist_id']."'>Playlist number ".$results[$i]['playlist_id']."</a> <br>";
    }
} else { 
    $main_links .=  $studio."<a href='playlist.php'>back </a> <br>";

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

    $table_body_html = process_template("playlist/table", ['ROWS_HTML' =>  $row_html]);

}

echo process_template("playlist/main", ['BODY_HTML' =>  $table_body_html,
'MAIN_LINKS' => $main_links]);

require __LAYOUT_FOOTER__;
