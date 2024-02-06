<?php
/**
 * plex web viewer
 */

 use Plex\Core\FileListing;
use Plex\Core\PlexSql;
use Plex\Template\Render;
use Plex\Template\Display;
use Plex\Template\Template;
use Plex\Template\Display\VideoDisplay;

define('TITLE', 'Home');
define('__SHOW_SORT__', true);
require_once '_config.inc.php';

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
    $vidInfo     = (new VideoDisplay('List'))->init('videoinfo');
    // $vidInfo->showVideoDetails = true;

    $body    = $vidInfo->Display($fileresults);
} else {
    $body['BODY'] = 'No duplicates Found';
}
$pageObj                        = true;

Template::echo('dupe/main', $body);
// Template::echo("artist/main",$PARAMS);
