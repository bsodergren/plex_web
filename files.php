<?php
require_once '_config.inc.php';

define('TITLE', 'Home');



$where = '';
// . ' AND ';
if (isset($_REQUEST['substudio'])) {
    // if  (!isset($_REQUEST['allfiles']))
    // {
    $substudio = str_replace('-', ' ', $_REQUEST['substudio']);
    // $substudio = str_replace("_","/",$substudio);
    $uri['substudio'] = [
        $_REQUEST['substudio'],
        $substudio,
    ];
    $studio_key       = 'substudio';
    // }
    // $studio_key="substudio";
}

if (isset($_REQUEST['studio'])) {
    $studio = str_replace('-', ' ', $_REQUEST['studio']);
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
    $genre        = str_replace('-', ' ', $_REQUEST['genre']);
    $genre        = str_replace('_', '/', $genre);
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

    if (!isset($_REQUEST['allfiles']) && $sql_studio != '') {
        $where = str_replace("studio = 'null'", 'studio IS NULL', $sql_studio);
    } else {
        $studio_key      = '';
        $uri['allfiles'] = $_GET['allfiles'];
        $where           = $sql_studio;
        $genre = '';
    }
}
$pageObj = new pageinate($where, $currentPage, $urlPattern);


// $sql = 'select (@row_num:=@row_num +1) AS result_number, id, video_key,filename,thumbnail,title,artist,genre,studio,substudio,duration,favorite,fullpath,library  from ( select id,video_key,filename,thumbnail,title,artist,genre,studio,substudio,duration,favorite,fullpath,library from metatags_filedb WHERE '.$where.' order by '.$order_sort.' LIMIT '.$offset.', '.$no_of_records_per_page.' ) t1, (select @row_num:='.$offset.') t2;';
$sql = query_builder('select', $where, false, $order_sort, $pageObj->itemsPerPage, $pageObj->offset);
logger('all files', $sql);

$results       = $db->query($sql);
$request_key   = uri_String($uri);
$total_results = $pageObj->totalRecords;

$redirect_string = 'files.php'.$request_key;

$referer_url = '';
if(basename($_SERVER["HTTP_REFERER"]) != 'home.php' ){

    $referer_url = $_SERVER["HTTP_REFERER"];
}

define('BREADCRUMB', ['home' => "home.php",'genre'=> $referer_url, $genre => '' ]);

require __LAYOUT_HEADER__;

?>      
<main role="main" class="container mt-5">
<?php
$page_array = [
    'total_files'     => $total_results,
    'redirect_string' => $redirect_string,
];


require __PHP_TEMPLATE__.'main_form.php';
?>
</main>
<?php
require __LAYOUT_FOOTER__;
