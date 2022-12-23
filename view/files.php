<?php

require_once "../_config.inc.php";

define('TITLE', "Home");

$redirect_string = "view/files.php?genre=" . $_REQUEST['genre'];
$studio_key='';


if (isset($_REQUEST['genre'])) {
    $genre = $_REQUEST['genre'];


    $genre = str_replace("-", " ", $genre);
    $genre = str_replace("_", "/", $genre);

    $uri["genre"] = array($_REQUEST['genre'], $genre);

    if ($genre == "NULL") {
        //$sql_genre = " and " ." genre IS NULL ";
        $sql_genre = "";
    } else {
        $sql_genre = " genre LIKE '%" . $genre . "%' ";
    }
}

    $order_sort = "  title " . $_SESSION['direction'];
    if (isset($_SESSION['sort'])) {
        $order_sort = $_SESSION['sort'] . " " . $_SESSION['direction'];
        $request_key = "&genre=" . $_REQUEST['genre'];

    }
    if (isset($_GET['current'])) {
        $uri["current"] = $_GET['current'];
    }

    $request_key = uri_String($uri);

    $where = $lib_where . " AND " . $sql_genre;
 logger('all files', $where);


    $pageObj = new pageinate($where,$currentPage,$urlPattern);
;

$sql=query_builder("select",$where,false,$order_sort,$pageObj->itemsPerPage,$pageObj->offset);

$results     = $db->query($sql);
$request_key = uri_String($uri);
$total_results=$pageObj->totalRecords;

logger('total_results', $total_results);
        include __LAYOUT_HEADER__;

    
  ?>
      
      <main role="main" class="container">
      
                  <?php
      
                  $page_array = [
                      'total_files'            => $total_results,
                      'redirect_string' => $redirect_string
                  ];
      
      
                  include __PHP_TEMPLATE__.'main_form.php'; ?>
      
      
      
      </main>
      <?php
      require __LAYOUT_FOOTER__;

