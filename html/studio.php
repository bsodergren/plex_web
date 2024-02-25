<?php

use Plex\Modules\Database\PlexSql;
use Plex\Template\Render;

/**
 * plex web viewer.
 */

require_once '_config.inc.php';

define('TITLE', 'View Studios');

if (isset($_REQUEST['substudio'])) {
    $studio_key = 'substudio';
    $studio_field = 'studio';
    $studio_text = $_REQUEST['substudio'];
} else {
    $studio_key = 'studio';
    $studio_field = 'substudio';
    $studio_text = $_REQUEST['studio'];
}

$studio = str_replace('-', ' ', $studio_text);
$studio = str_replace('_', '/', $studio);
$order = $studio_field.' ASC';

$sql_studio = $studio_key." = '".$studio."'";
$sql = PlexSql::query_builder(Db_TABLE_VIDEO_TAGS,
    'DISTINCT('.$studio_field.') as '.$studio_field.' ',
    $sql_studio,
    $studio_field,
    $order
);

$request_key = $studio_key.'='.$studio_text;

// dump($sql);

$result = $db->query($sql);
$rows = count($result);

$all_url = 'files.php?'.$request_key.'&allfiles=1';

// dump([$studio,$studio_field,$studio_key]);
foreach ($result as $k => $v) {
    $len = strlen($studio) * 2;
    // $v["cnt"]=1; ".$v["cnt"]."

    if ('' != $v[$studio_field]) {
        if ('studio' == $studio_field) {
            // Render::return('elements/html/a',[]);
            $body .= $v[$studio_field]." <a href='genre.php?".$studio_key.'='.$studio."'>".$studio.'</a> '.$v['cnt'].'<br>'."\n";
        } else {
            $body .= $studio." <a href='genre.php?".$studio_field.'='.$v[$studio_field].'&prev='.$studio_text."'>".$v[$studio_field].'</a> '.$v['cnt'].'<br>'."\n";
        }
    } else {
        $char = '&nbsp;';
        $body .= str_repeat($char, $len)."<a href='genre.php?studio=".$studio."'>".$studio.'</a> <br>'."\n";
    }
}

Render::Display($body);
