<?php
/**
 * Command like Metatag writer for video files.
 */

require_once '_config.inc.php';
define('GRID_VIEW', true);

define('TITLE', 'search');

if (isset($_REQUEST['genre'])) {
    $_REQUEST['query'] = implode(',', $_REQUEST['genre']);
}

if (isset($_REQUEST['sort'])) {
    $uri['sort'] = $_REQUEST['sort'];
}

if (isset($_REQUEST['direction'])) {
    $uri['direction'] = $_REQUEST['direction'];
}

if (isset($_REQUEST['query'])) {
    $uri['query'] = $_REQUEST['query'];
}
if (isset($_REQUEST['searchField'])) {
    $uri['searchField'] = $_REQUEST['searchField'];
}

if (isset($uri)) {
    $request_key   = uri_String($uri);
    if (array_key_exists('sort', $uri)) {
        $order_sort = $uri['sort'].' '.$uri['direction'];
    }
}

$redirect_string = 'search.php'.$request_key;

if ('Search' == $_REQUEST['submit'] || isset($_REQUEST['query'])) {

    $res            = searchDBVideos($_REQUEST);

    $where          = $res[0];
    $queryArr       = $res[1];
    foreach ($queryArr as $key => $value) {
        $keys[] = $key.'  !!primary,5!!'.$value.'!! ';
    }

    $pageObj        = new pageinate($where, $currentPage, $urlPattern);

    $sql            = query_builder('id', $where, false, $order_sort);
    $results        = $db->query($sql);
    foreach($results as $n => $row){
        $playlist_ids[] = $row['id'];
    }

    $playlist_ids_str = implode(",",$playlist_ids);

    $sql            = query_builder('select', $where, false, $order_sort, $pageObj->itemsPerPage, $pageObj->offset);
    $results        = $db->query($sql);

    $msg            = 'Showing '.$pageObj->totalRecords.' results for for '.implode(',, ', $keys);
    $html_msg       = process_template('search/search_msg', ['SQL' => str_replace('WHERE', 'WHERE<br>', $sql),   'MSG' => $msg]);
    //  $html_msg .= process_template("search/search_msg", [   'MSG' => $sql] );
    $search_results =  gridview($results);

    //   $search_results =     display_filelist($results, '', $page_array);
}
$search_types    = [
    'studio',
    'substudio',
    'artist',
    'title',
    'keyword',
    'genre',
];

foreach ($search_types as $key) {
    // $checkbox = draw_checkbox("searchField[]", $key, $key);
    $checkboxes .= process_template('search/checkboxes', ['NAME' => $key]);
}

$body            = process_template('search/search', [
    'HIDDEN_IDS'    => add_hidden("playlist",$playlist_ids_str),
    'HIDDEN_STUDIO'    => add_hidden("studio",$_REQUEST['query']." Search"),
    'SEARCH_RESULTS' => $search_results,
    'CHECKBOXES'     => $checkboxes,
    'HTML_MSG'       => $html_msg,
]);

include_once __LAYOUT_HEADER__;

template::echo('base/page', ['BODY' => $body]);

include_once __LAYOUT_FOOTER__;
