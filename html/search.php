<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';
define('GRID_VIEW', true);
$playlist_ids    = [];
define('TITLE', 'search');

foreach ($tag_array as $tag) {
    if (isset($_REQUEST[$tag])) {
        if (is_array($_REQUEST[$tag])) {
            $queries[] = implode(',', $_REQUEST[$tag]);
        }
    }
}

if (is_array($_REQUEST['query'])) {
    $_REQUEST['query'] = implode(',', $queries);
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
    $request_key = uri_String($uri);
    if (array_key_exists('sort', $uri)) {
        $order_sort = $uri['sort'].' '.$uri['direction'];
    }
}

$redirect_string = 'search.php'.$request_key;

if ('Search' == $_REQUEST['submit'] || isset($_REQUEST['query'])) {
    // dump($_REQUEST);
    $search             = new FileListing($_REQUEST, $currentPage, $urlPattern);

    [$results,$pageObj] = $search->getSearchResults($_REQUEST['field'], $_REQUEST['query']);

    foreach ($results as $n => $row) {
        $playlist_ids[] = $row['id'];
    }

    $playlist_ids_str   = implode(',', $playlist_ids);
    // $msg              = 'Showing '.$pageObj->totalRecords.' results for for '.implode(',, ', $keys);

    $msg                = 'Showing '.count($results).' results for for '.$_REQUEST['query'];
    $msg                = strtolower(str_replace('-', '.', $msg));
    $msg                = strtolower(str_replace('_', ' ', $msg));
    $html_msg           = Template::GetHTML('search/search_msg', ['MSG' => $msg]);
    //  $html_msg .= Template::GetHTML("search/search_msg", [   'MSG' => $sql] );

    $grid               = new GridDisplay();
    $search_results     = $grid->gridview($results, count($results));

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
    // $checkbox = Render::draw_checkbox("searchField[]", $key, $key);
    $checkboxes .= Template::GetHTML('search/checkboxes', ['NAME' => $key]);
}

$body            = Template::GetHTML('search/search', [
    'HIDDEN_IDS'     => Render::add_hidden('playlist', $playlist_ids_str),
    'HIDDEN_STUDIO'  => Render::add_hidden('studio', $_REQUEST['query'].' Search'),
    'SEARCH_RESULTS' => $search_results,
    'CHECKBOXES'     => $checkboxes,
    'HTML_MSG'       => $html_msg,
]);

include_once __LAYOUT_HEADER__;

template::echo('base/page', ['BODY' => $body]);

include_once __LAYOUT_FOOTER__;
