<?php
require_once '_config.inc.php';
define('TITLE', 'View Genres');

$null        = '';
$null_req    = '&';
$sql_studio  = 'library';

if (isset($_REQUEST['allfiles'])) {
} else {
    if (isset($_REQUEST['substudio']) && 'null' != $_REQUEST['substudio']) {
        $studio_key       = 'substudio';

        $studio_text      = $_REQUEST['substudio'];

        $studio           = urldecode($studio_text);
        $studio_sql_query = $studio_key." = '".$studio."' ";
    } else {
        if (isset($_REQUEST['substudio']) && 'null' == $_REQUEST['substudio']) {
            $null     = ' and substudio is null ';
            $null_req = '&substudio=null';
        }

        $studio_key       = 'studio';
        $studio_text      = $_REQUEST['studio'];

        $studio           = urldecode($studio_text);
        $studio_sql_query = $studio_key." = '".$studio."' ";

        if ('NULL' == $_REQUEST['studio']) {
            $studio_sql_query = $studio_key.' IS NULL ';
        }
    } // end if

    // $studio = urldecode($studio_text);
    // $studio_sql_query = $studio_key . " = '" . $studio . "' ";

    $sql_studio  = $studio_sql_query.$null;

    $request_key = $studio_key.'='.$studio_text.$null_req;
}
$order       = 'genre ASC';
$sql         = query_builder(
    'DISTINCT(genre) as genre, count(genre) as cnt ',
    $sql_studio,
    'genre',
    $order
);
$genre_array = [];
logger('qyefasd', $sql);
$result      = $db->query($sql);

$all_url     = 'files.php?'.$request_key.'allfiles=1';

$rows        = count($result);

if ($rows <= 1) {
    //    JavaRefresh($all_url, 0);
}

foreach ($result as $k => $v) {
    $row_genre_array = explode(',', $v['genre']);
    $genre_array     = array_merge($genre_array, $row_genre_array);
}

$genre_array = array_unique($genre_array);
$studio_url  = 'studio.php?studio='.$_REQUEST['prev'];

require __LAYOUT_HEADER__;
?>
<main role="main" class="container">

<?php
asort($genre_array);
foreach ($genre_array as $k => $v) {
    // $v["cnt"]=1; ".$v["cnt"]."
    if ('' != $v) {
        if (isset($studio_key) && isset($studio)) {
            $db->where($studio_key, $studio, 'like');
        }
        $db->where('genre', '%'.$v.'%', 'like');
        $db->where('library', $_SESSION['library'], 'like');
        $count = $db->getOne('metatags_filedb ', 'count(*) as cnt');

        echo $studio." <a href='files.php?".$request_key.'genre='.urlencode($v)."'>".$v.'</a> '.$count['cnt'].' <br>';
    }
}

?>
</main>
<?php
require __LAYOUT_FOOTER__;
