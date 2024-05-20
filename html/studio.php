<?php

use Plex\Modules\Database\PlexSql;
use Plex\Template\Render;

/**
 * plex web viewer.
 */

require_once '_config.inc.php';

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
$studio = str_replace('_', '_', $studio);
$order = $studio_field.' ASC';

$sql_studio = $studio_key." = '".$studio."'";
$sql = PlexSql::query_builder(Db_TABLE_VIDEO_METADATA,
    'DISTINCT('.$studio_field.') as '.$studio_field.',count('.$studio_field.') as cnt ',
    $sql_studio,
    $studio_field,
    $order
);
$studio_html = '';
$studio_links = '';
$request_key = $studio_key.'='.$studio_text;

$result = $db->query($sql);
$rows = count($result);
// dump($result);
$all_url = 'list.php?'.$request_key.'&allfiles=1';

foreach ($result as $k => $v) {
    UtmDump([$k, $v]);
    $len = strlen($studio) * 2;
    // $v["cnt"]=1; ".$v["cnt"]."
    $link_array = [];
    $link_array['url'] = 'genre';
    if ('' != $v[$studio_field]) {
        if ('studio' == $studio_field) {
            $studio_name = $v[$studio_field];
            // Render::return('elements/html/a',[]);
            // $link_array['prefix'] = $v[$studio_field];
            $link_array['GET_REQUEST'] = $studio_key.'='.urlencode($studio);
            $link_array['NAME'] = '1 '.$studio;
            $link_array['COUNT'] = $v['cnt'];
        } else {
            // $link_array['prefix'] = $studio_text;
            $link_array['GET_REQUEST'] = $studio_field.'='.urlencode($v[$studio_field]).'&studio='.
            urlencode($studio_text);
            $link_array['NAME'] = $v[$studio_field];
            $link_array['COUNT'] = $v['cnt'];
        }
    } else {
        $link_array['prefix'] = str_repeat('&nbsp;', $len);
        $link_array['GET_REQUEST'] = 'studio='.urlencode($studio);
        $link_array['NAME'] = $studio;
       $link_array['COUNT'] = $v['cnt'];
    }
    $studio_links .= Render::html(
        'pages/Studio/link',
        $link_array);
}

$html_links = Render::html('pages/Studio/group', [
    'STUDIO_LINK' => $studio_links,
]);

$studio_html = Render::html('pages/Studio/block', [
    'STUDIO_LINKS' => $html_links,
]);

$body = Render::html('pages/Studio/Library', [
    'LIBRARY_NAME' => $studio,
    'STUDIO_BOX_HTML' => $studio_html,
]);

Render::Display($body, 'pages/Studio/body');
