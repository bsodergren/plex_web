<?php
require_once("_config.inc.php");

define('TITLE', "search");
if (isset($_REQUEST['genre'])) {
    $_REQUEST['field'] = 'genre';
    $_REQUEST['query'] = implode(",",$_REQUEST['genre']);
}

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

if ($_REQUEST['submit'] == "Search" || isset($_REQUEST['query'])) {

    $query = $_REQUEST['query'];
    $query = str_replace("+"," ",$query);

    if(isset($_REQUEST['field'])) {
        if(str_contains($query,",")) {
            $query_array = explode(",",$query);
            foreach($query_array as $q){
                $whereArray[] = $_REQUEST['field']." LIKE '%" . $q . "%' ";               
            }
            $where = implode(" AND ", $whereArray);
        } else {
            $where = $_REQUEST['field']." LIKE '%" . $query . "%' ";
        }
        $keyword = " ".$_REQUEST['field'] ." named ";
    } else {
        $where = " ((filename LIKE '%" . $query . "%') OR ";
        $where .= " (title LIKE '%" . $query . "%') OR ";
        $where .= " (artist LIKE '%" . $query . "%') OR ";
        $where .= " (genre LIKE '%" . $query . "%') OR ";
        $where .= " (studio LIKE '%" . $query . "%') OR ";
        $where .= " (substudio LIKE '%" . $query . "%') OR ";
        $where .= " (keyword LIKE '%" . $query . "%')) ";

    }

    $pageObj = new pageinate($where, $currentPage, $urlPattern);

    $sql = query_builder('select', $where, false, $order_sort, $pageObj->itemsPerPage, $pageObj->offset);
    $results = $db->query($sql);

    $page_array = [
        'total_files'     => $pageObj->totalRecords,
        'redirect_string' => $redirect_string,
    ];

        $msg = "Showing ".$pageObj->totalRecords." results for for ".$keyword." $query";
        $html_msg = process_template("search/search_msg", [   'MSG' => $msg] );
      #  $html_msg .= process_template("search/search_msg", [   'MSG' => $sql] );
    $search_results = display_filelist($results, '', $page_array);
}
include_once __LAYOUT_HEADER__;

echo process_template("search/search", ['SEARCH_RESULTS' => $search_results,
'HTML_MSG' => $html_msg] );


include_once __LAYOUT_FOOTER__;
