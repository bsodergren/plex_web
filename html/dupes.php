<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';

define('TITLE', 'Home');

include __LAYOUT_HEADER__;
$column                         = 'duration';
$url_array['sort_types']['Key'] = 'f.video_key';
unset($url_array['sort_types']['Genre'], $url_array['sort_types']['Title'], $url_array['sort_types']['Studio'], $url_array['sort_types']['Sub Studio'], $url_array['sort_types']['Artist']);

if (isset($_GET['sort'])) {
    [$p,$field] = explode('.', $_GET['sort']);
    if ('f' == $p) {
        $column = $field;
    }
}

$psql                           = new PlexSql();
$results                        = $psql->getDuplicates($column);
if (count($results) > 0) {
    foreach ($results as $k => $value) {
        $dup_result[] = $psql->showDupes($column, $value[$column]);
    }
    foreach ($dup_result as $k => $v) {
        foreach ($v as $kv => $row) {
            $fileresults[] = $row;
        }
    }
    // define('NONAVBAR',true);
    $vidInfo = new VideoDisplay('videoinfo');
    // $vidInfo->showVideoDetails = true;

    $body    = $vidInfo->filelist($fileresults);
} else {
    $body = 'No duplicates Found';
}
template::echo('dupe/main', ['BODY' => $body]);
// Template::echo("artist/main",$PARAMS);
$pageObj                        = true;

include __LAYOUT_FOOTER__;
