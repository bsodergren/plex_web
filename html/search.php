<?php


 use Plex\Template\Render;
 use Plex\Core\FileListing;
 use Plex\Core\ProcessForms;
 
 use Plex\Template\Display\VideoDisplay;
 use Plex\Template\HTML\Elements;
 use Plex\Core\PlexSql;
use Plex\Core\Request;
use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;

require_once '_config.inc.php';
define('GRID_VIEW', true);
$playlist_ids    = [];
define('TITLE', 'search');
define('USE_FILTER', false);
define("ALPHA_SORT",true) ;

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
    $search             = new FileListing(new Request);

    [$results,$pageObj] = $search->getSearchResults($_REQUEST['field'], $_REQUEST['query']);

    foreach ($results as $n => $row) {
        $playlist_ids[] = $row['id'];
    }

    $playlist_ids_str   = implode(',', $playlist_ids);
    // $msg              = 'Showing '.$pageObj->totalRecords.' results for for '.implode(',, ', $keys);
    $view = 'Grid';
if(array_key_exists('view',$_REQUEST)){
    $view = $_REQUEST['view'];
}
    $msg                = 'Showing '.count($results).' results for for '.$_REQUEST['query'];
    $msg                = strtolower(str_replace('-', '.', $msg));
    $msg                = strtolower(str_replace('_', ' ', $msg));
    $html_msg           = Render::html('search/search_msg', ['MSG' => $msg]);
    //  $html_msg .= Render::html("search/search_msg", [   'MSG' => $sql] );


    $grid                 = (new VideoDisplay($view ))->init();
$search_results         = $grid->Display($results, [  'total_files'     => count($results)]);


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
    // $checkbox = Elements::draw_checkbox("searchField[]", $key, $key);
    $checkboxes .= Render::html('search/checkboxes', ['NAME' => $key]);
}

$body            = Render::html('search/search', [
    'HIDDEN_IDS'     => Elements::add_hidden('playlist', $playlist_ids_str),
    'HIDDEN_STUDIO'  => Elements::add_hidden('studio', $_REQUEST['query'].' Search'),
    'SEARCH_RESULTS' => $search_results,
    'CHECKBOXES'     => $checkboxes,
    'HTML_MSG'       => $html_msg,
]);


Render::Display($body);
