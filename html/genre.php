<?php

use Plex\Modules\Database\PlexSql;
use Plex\Template\Render;
use UTMTemplate\HTML\Elements;

require_once '_config.inc.php';
$body = '';
$null = '';
$null_req = '&';
$sql_studio = 'library';

if (isset($_REQUEST['allfiles'])) {
} else {
    if (isset($_REQUEST['substudio']) && 'null' != $_REQUEST['substudio']) {
        $studio_key = 'substudio';

        $studio_text = $_REQUEST['substudio'];

        $studio = urldecode($studio_text);
        $studio_sql_query = $studio_key." = '".$studio."' ";
    } else {
        if (isset($_REQUEST['substudio']) && 'null' == $_REQUEST['substudio']) {
            $null = ' and substudio is null ';
            $null_req = '&substudio=null' . $null_req ;
        }

        $studio_key = 'studio';
        $studio_text = $_REQUEST['studio'];
        $studio = urldecode($studio_text);

        $studio_sql_query = $studio_key." = '".$studio."' ";

        if ('NULL' == $_REQUEST['studio']) {
            $studio_sql_query = $studio_key.' IS NULL ';
        }
    } // end if

    // $studio = urldecode($studio_text);
    // $studio_sql_query = $studio_key . " = '" . $studio . "' ";

    $sql_studio = $studio_sql_query.$null;

    $request_key = $studio_key.'='.urlencode($studio_text).$null_req;
}
$order = 'genre ASC';
$sql = PlexSql::query_builder(Db_TABLE_VIDEO_METADATA,
    'DISTINCT(genre) as genre, count(genre) as cnt ',
    $sql_studio,
    'genre',
    $order
);

$genre_array = [];
$result = $db->query($sql);

$rows = count($result);

if ($rows <= 1) {
    //    Elements::javaRefresh($all_url, 0);
}

foreach ($result as $k => $v) {
    $row_genre_array = explode(',', $v['genre']);
    $genre_array = array_merge($genre_array, $row_genre_array);
}

$genre_array = array_unique($genre_array);

$genre_links = Render::html(
    'pages/Genre/link',[
    'url' => 'list',
    'NAME' => 'All',
    'GET_REQUEST' => $request_key,
    ]
);
asort($genre_array);
utminfo($genre_array);
foreach ($genre_array as $k => $v) {
    $link_array = [];
    // $v["cnt"]=1; ".$v["cnt"]."
    if ('' != $v) {
        if (isset($studio_key, $studio)) {
            $db->where($studio_key, $studio, 'like');
        }
        $db->where('genre', '%'.$v.'%', 'like');
        if ('All' != $_SESSION['library']) {
            $db->where('library', $_SESSION['library'], 'like');
        }
        if($null != '') {
            $db->where("substudio", NULL, 'IS');
        }
        $count = $db->getOne(Db_TABLE_VIDEO_METADATA, 'count(*) as cnt');
        $link_array['url'] = 'list';

        $link_array['GET_REQUEST'] = $request_key.'genre='.urlencode($v);
        $link_array['NAME'] = $v;
        $link_array['COUNT'] = $count['cnt'];
        $genre_links .= Render::html(
            'pages/Genre/link',
            $link_array
     );
    }
}


$html_links = Render::html('pages/Genre/group', [
    'STUDIO_LINK' => $genre_links,
]);

$genre_html = Render::html('pages/Genre/block', [
    'STUDIO_LINKS' => $html_links,
]);

$body = Render::html('pages/Genre/Library', [
    'LIBRARY_NAME' => $studio,
    'STUDIO_BOX_HTML' => $genre_html,
]);

Render::Display($body, 'pages/Genre/body');
