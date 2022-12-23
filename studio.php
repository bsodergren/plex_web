<?php

require_once '_config.inc.php';

define('TITLE', 'View Studios');



require __LAYOUT_HEADER__;

$lib_where = $lib_where . ' AND ';

$null = '';

if (isset($_REQUEST['substudio'])) {
    $studio_key  = 'substudio';
    $studio_text = $_REQUEST['substudio'];
} else {
    $studio_key  = 'studio';
    $studio_text = $_REQUEST['studio'];
    // $null=' and substudio is null ';
}

$studio = str_replace('-', ' ', $studio_text);
$studio = str_replace('_', '/', $studio);

$sql_studio = $lib_where.$studio_key." = '".$studio."'";

$request_key = $studio_key.'='.$studio_text;

$order = 'genre ASC';
$sql   = query_builder(
    'DISTINCT(genre) as genre, count(genre) as cnt ',
    $sql_studio,
    'genre',
    $order
);


$result = $db->query($sql);


?>

<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>
<a href='genre.php?<?php echo $request_key; ?>&genre=NULL'>All</a><br>

<?php
foreach ($result as $k => $v) {
    // $v["cnt"]=1; ".$v["cnt"]."
    if ($v['genre'] != '') {
        echo $studio." <a href='genre.php?".$request_key.'&genre='.$v['genre']."'>".$v['genre'].'</a> '.$v['cnt'].'<br>';
    }
}

?>
</main>
<?php
require __LAYOUT_FOOTER__;
