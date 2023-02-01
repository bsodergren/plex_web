<?php
require_once '_config.inc.php';
define('TITLE', 'View Genres');

$null     = '';
$null_req = '';

if (isset($_REQUEST['substudio']) && $_REQUEST['substudio'] != 'null') {
    $studio_key  = 'substudio';
    $studio_text = $_REQUEST['substudio'];
    $studio      = str_replace('-', ' ', $studio_text);
    $studio      = str_replace('_', '/', $studio);

    $studio_sql_query = $studio_key." = '".$studio."' ";
} else {
    if (isset($_REQUEST['substudio']) && $_REQUEST['substudio'] == 'null') {
        $null     = ' and substudio is null ';
        $null_req = '&substudio=null';
    }

    $studio_key  = 'studio';
    $studio_text = $_REQUEST['studio'];

    $studio = str_replace('-', ' ', $studio_text);
    $studio = str_replace('_', '/', $studio);

    $studio_sql_query = $studio_key." = '".$studio."' ";

    if ($_REQUEST['studio'] == 'NULL') {
        $studio_sql_query = $studio_key.' IS NULL ';
    }
}//end if

$studio = str_replace('-', ' ', $studio_text);
$studio = str_replace('_', '/', $studio);

$sql_studio = $studio_sql_query.$null;

$request_key = $studio_key.'='.$studio_text.$null_req;

$order = 'genre ASC';
$sql   = query_builder(
    'DISTINCT(genre) as genre, count(genre) as cnt ',
    $sql_studio,
    'genre',
    $order
);
$genre_array = [];
logger('qyefasd', $sql);
$result = $db->query($sql);

foreach ($result as $k => $v) {
    $row_genre_array = explode(",", $v['genre']);
    $genre_array = array_merge($genre_array, $row_genre_array);
}

$genre_array = array_unique($genre_array);

$all_url = 'files.php?' . $request_key . '&allfiles=1';
DEFINE('BREADCRUMB', ['home' => "home.php",'genre'=> '', 'all' => $all_url]);
require __LAYOUT_HEADER__;
?>
<main role="main" class="container">

<?php
foreach ($genre_array as $k => $v) {
    // $v["cnt"]=1; ".$v["cnt"]."
    if ($v != '') {
        echo $studio."<a href='files.php?".$request_key.'&genre='.$v."'>".$v.'</a> <br>';
    }
}

?>
</main>
<?php
require __LAYOUT_FOOTER__;
