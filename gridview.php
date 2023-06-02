<?php
require_once '_config.inc.php';

define('TITLE', 'Home');


define('GRID_VIEW',true);

$where = '';
// . ' AND ';
if (isset($_REQUEST['substudio'])) {
    // if  (!isset($_REQUEST['allfiles']))
    // {
    $substudio = urldecode($_REQUEST['substudio']);
    $uri['substudio'] = [
        $_REQUEST['substudio'],
        $substudio,
    ];
    $studio_key       = 'substudio';
    // }
    // $studio_key="substudio";
}

if (isset($_REQUEST['studio'])) {
    $studio = urldecode( $_REQUEST['studio']);
    // $studio = str_replace("_","/",$studio);
    $uri['studio'] = [
        $_REQUEST['studio'],
        $studio,
    ];
    if (!isset($studio_key)) {
        $studio_key = 'studio';
    }
}

if (isset($_REQUEST['genre'])) {
    $genre        = urldecode($_REQUEST['genre']);
    $uri['genre'] = [
        $_REQUEST['genre'],
        $genre,
    ];
}

if (isset($_SESSION['sort'])) {
    $uri['sort'] = $_SESSION['sort'];
}

if (isset($_SESSION['direction'])) {
    $uri['direction'] = $_SESSION['direction'];
}

if (isset($uri)) {
    $sql_studio = '';
    $res_array  = uri_SQLQuery($uri);
    
    if (key_exists('sort', $res_array)) {
        $order_sort = $res_array['sort'];
    }

    if (key_exists('sql', $res_array)) {
        $sql_studio = $res_array['sql'];
    }

    if (isset($_REQUEST['genre'])) {
        $where = str_replace("genre  = '".$_REQUEST['genre']."'", 'genre like \'%'.$_REQUEST['genre'].'%\'', $sql_studio);

    } 
    if (!isset($_REQUEST['allfiles']) && $sql_studio != '') {
        $where = str_replace("studio = 'null'", 'studio IS NULL', $sql_studio);

    } else {
        $studio_key      = '';
        $uri['allfiles'] = $_GET['allfiles'];
        $where           = $sql_studio;
        $genre = '';
    }
}


if (isset($_REQUEST['genre'])) {
    $where = str_replace("genre = '".$_REQUEST['genre']."'", 'genre like \'%'.$_REQUEST['genre'].'%\'', $where);

} 

$pageObj = new pageinate($where, $currentPage, $urlPattern);

$sql = query_builder('select', $where, false, $order_sort, $pageObj->itemsPerPage, $pageObj->offset);
logger('all files', $sql);

$results       = $db->query($sql);
$request_key   = uri_String($uri);


$redirect_string = __THIS_FILE__.  $request_key;

$referer_url = '';
if (basename($_SERVER["HTTP_REFERER"]) != 'home.php') {

    $referer_url = $_SERVER["HTTP_REFERER"];
}

define('BREADCRUMB', ['home' => "home.php", 'genre' => 'genre.php?allfiles=1', $genre => '']);

require __LAYOUT_HEADER__;

    $page_array = [
        'total_files'     => $pageObj->totalRecords ,
        'redirect_string' => $redirect_string,
    ];

//    echo display_filelist($results, '', $page_array); 

$table_body_html =  gridview($results);

echo process_template("page", ['BODY' =>  $table_body_html]);

 require __LAYOUT_FOOTER__;
