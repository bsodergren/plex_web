<?php

use Plex\Modules\Database\PlexSql;

define('__SHOW_SORT__', true);

require_once '_config.inc.php';

$sql = PlexSql::query_builder(Db_TABLE_VIDEO_FILE, 'id,video_key', 1);
$res = $db->query($sql);
$lib = $_SESSION['library'];

foreach ($res as $row => $val) {
    $query = 'insert into metatags_sequence (seq_id,video_id,video_key,Library) values ';
    $query .= " (nextval('".$lib."'),".$val['id'].",'".$val['video_key']."','".$lib."')";

    dump($query);
    exit;
}
echo 'done';
