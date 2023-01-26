<?php
require_once("_config.inc.php");

define('TITLE', "search");

if (isset($_SESSION['sort'])) {
    $uri['sort'] = $_SESSION['sort'];
}

if (isset($_SESSION['direction'])) {
    $uri['direction'] = $_SESSION['direction'];
}

if (isset($_REQUEST['query'])) {
    $uri['query'] = $_REQUEST['query'];
}

if (isset($uri)) {
    $request_key   = uri_String($uri);
}

$redirect_string = 'search.php' . $request_key;

include_once __LAYOUT_HEADER__;

echo '<main role="main" class="container mt-5">';
echo process_template("search", []);


if ($_REQUEST['submit'] == "Search" || isset($_REQUEST['query'])) {

$query = $_REQUEST['query'];
    $where =  " ((filename LIKE '%" . $query . "%') OR ";
    $where .=  " (title LIKE '%" . $query . "%') OR ";
    $where .=  " (artist LIKE '%" . $query . "%') OR ";
    $where .=  " (genre LIKE '%" . $query . "%') OR ";
    $where .=  " (studio LIKE '%" . $query . "%') OR ";
    $where .=  " (substudio LIKE '%" . $query . "%')) ";
    $pageObj = new pageinate($where, $currentPage, $urlPattern);

    $sql = query_builder('select', $where, false, $order_sort, $pageObj->itemsPerPage, $pageObj->offset);
    $results = $db->query($sql);

    $page_array = [
        'total_files'     => $pageObj->totalRecords,
        'redirect_string' => $redirect_string,
    ];
    echo  display_filelist($results, '', $page_array);

}

echo '</main>';

include_once __LAYOUT_FOOTER__;
