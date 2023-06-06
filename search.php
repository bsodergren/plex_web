<?php
require_once("_config.inc.php");
define('GRID_VIEW',true);


define('TITLE', "search");

if (isset($_REQUEST['genre'])) {
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

 
    $res = searchDBVideos($_REQUEST);
    $where= $res[0];
    $queryArr = $res[1];
    foreach($queryArr as $key => $value)
    {
        $keys[] = $key."  !!primary,5!!". $value. "!! ";
        
    }

    $pageObj = new pageinate($where, $currentPage, $urlPattern);

    $sql = query_builder('select', $where, false, $order_sort, $pageObj->itemsPerPage, $pageObj->offset);
    $results = $db->query($sql);
    $page_array = [
        'total_files'     => $pageObj->totalRecords,
        'redirect_string' => $redirect_string,
    ];

        $msg = "Showing ".$pageObj->totalRecords." results for for " . implode(",, ",$keys);
        $html_msg = process_template("search/search_msg", ['SQL'=>str_replace("WHERE","WHERE<br>",$sql),   'MSG' => $msg] );
      #  $html_msg .= process_template("search/search_msg", [   'MSG' => $sql] );
      $search_results =  gridview($results);

 //   $search_results = display_filelist($results, '', $page_array);
}
include_once __LAYOUT_HEADER__;

$body = process_template("search/search", ['SEARCH_RESULTS' => $search_results,
'HTML_MSG' => $html_msg] );

$template->render("page",['BODY' => $body]);

include_once __LAYOUT_FOOTER__;
