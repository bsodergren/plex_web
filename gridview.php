<?php
require_once '_config.inc.php';

define('TITLE', 'Home');



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
print_r($sql);
$results       = $db->query($sql);
$request_key   = uri_String($uri);


$redirect_string = __THIS_FILE__.  $request_key;

$referer_url = '';
if (basename($_SERVER["HTTP_REFERER"]) != 'home.php') {

    $referer_url = $_SERVER["HTTP_REFERER"];
}

define('BREADCRUMB', ['home' => "home.php", 'genre' => 'genre.php?allfiles=1', $genre => '']);

require __LAYOUT_HEADER__;

?>

<main role="main" class="container mt-5">
    <?php
    $page_array = [
        'total_files'     => $pageObj->totalRecords ,
        'redirect_string' => $redirect_string,
    ];

//    echo display_filelist($results, '', $page_array); 


$r = 0;

for ($i = 0; $i < count($results); $i++) {

    $file_info = match ($_SESSION['sort']) {
      'genre' => $results[$i]['genre'],
      'duration' => videoDuration($results[$i]['duration']),
      'studio' => $results[$i]['studio'],
      'artist' => $results[$i]['artist'],
      'title' => $results[$i]['title'],
      'added' => $results[$i]['added'],
      'filename' => $results[$i]['filename'],

    };


    $cell_html .= process_template(
        "grid/cell",
        [
            'THUMBNAIL' => $results[$i]['thumbnail'],
            'ROW_ID' =>  $results[$i]['id'],
            'FILE_INFO'  => $file_info,
        ]
    );
}

$row_html =  process_template("grid/row", ['ROW_CELLS' => $cell_html]);

$table_body_html = process_template("grid/table", ['ROWS_HTML' =>  $row_html,
'INFO_NAME' => $_SESSION['sort'],
]);

echo process_template("grid/main", ['BODY_HTML' =>  $table_body_html]);


  ?>
</main>
<?php require __LAYOUT_FOOTER__;
