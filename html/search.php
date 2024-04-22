<?php

use Plex\Core\Request;
use Plex\Modules\Database\FileListing;
use Plex\Modules\Display\VideoDisplay;
use Plex\Template\Render;
use UTMTemplate\HTML\Elements;

require_once '_config.inc.php';
$playlist_ids = [];
$queries      = [];
utmdump($_REQUEST);
foreach (Request::$tag_array as $tag) {
    if (isset($_REQUEST[$tag])) {
        if (is_array($_REQUEST[$tag])) {
            $queries = $_REQUEST[$tag];
            continue;
        }
        $queries[] = $_REQUEST[$tag];
    }
}

if (!is_array($_REQUEST['field'])) {
    $fieldArray[]      = $_REQUEST['field'];
    $_REQUEST['field'] = $fieldArray;
}


if (count($queries) > 0) {
    $_REQUEST['query'] = $queries;
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

if ('Search' == isset($_REQUEST['submit']) || isset($_REQUEST['query'])) {
    $search = new FileListing(new Request());

    [$results,$pageObj] = $search->getSearchResults($_REQUEST['field'][0], $_REQUEST['query']);
utmdump($results);
    foreach ($results as $n => $row) {
        $playlist_ids[] = $row['id'];
    }

    $playlist_ids_str = implode(',', $playlist_ids);
    //  $msg              = 'Showing '.$pageObj->totalRecords.' results for for '.implode(',, ', $keys);
    $view = 'Grid';
    if (array_key_exists('view', $_REQUEST)) {
        $view = $_REQUEST['view'];
    }
    if(is_array($_REQUEST['query'])) {
        $string = implode(", ", $_REQUEST['query']);
    } else {
        $string = $_REQUEST['query'];
    }
   // utmdump($_REQUEST['query']);
    $msg      = 'Showing '.$pageObj->totalRecords.' results for for '.$string;
    $msg      = strtolower(str_replace('-', '.', $msg));
    $msg      = strtolower(str_replace('_', ' ', $msg));
    $html_msg = Render::html('pages/search/search_msg', ['MSG' => $msg]);
    //  $html_msg .= Render::html("search/search_msg", [   'MSG' => $sql] );

    $grid           = (new VideoDisplay($view))->init();
    $search_results = $grid->getDisplay($results, ['total_files' => $pageObj->totalRecords]);

    //   $search_results =     display_filelist($results, '', $page_array);
}
$search_types = [
    'studio',
    'substudio',
    'artist',
    'title',
    'keyword',
    'genre',
];
$checkboxes = '';
foreach ($search_types as $key) {
    // $checkbox = Elements::draw_checkbox("searchField[]", $key, $key);
    $checkboxes .= Render::html('pages/search/checkboxes', ['NAME' => $key]);
}

$body = Render::html('pages/search/search', [
    'HIDDEN_IDS'     => Elements::add_hidden('playlist', $playlist_ids_str),
    'HIDDEN_STUDIO'  => Elements::add_hidden('studio', $string.' Search'),
    'SEARCH_RESULTS' => $search_results,
    'CHECKBOXES'     => $checkboxes,
    'HTML_MSG'       => $html_msg,
]);

Render::Display($body,'pages/search/body');
