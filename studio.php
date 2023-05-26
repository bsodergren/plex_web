<?php

require_once '_config.inc.php';

define('TITLE', 'View Studios');



require __LAYOUT_HEADER__;

$lib_where = $lib_where . '  ';

$null = '';

if (isset($_REQUEST['substudio'])) {
    $studio_key  = 'substudio';
    $studio_field =  'substudio';

    $studio_text = $_REQUEST['substudio'];
} else {
    $studio_key  = 'studio';
    $studio_field =  'substudio';

    $studio_text = $_REQUEST['studio'];
    // $null=' and substudio is null ';
}

$studio = str_replace('-', ' ', $studio_text);
$studio = str_replace('_', '/', $studio);

$sql_studio = $lib_where.$studio_key." = '".$studio."'";

$request_key = $studio_key.'='.$studio_text;

$order = $studio_field.' ASC';
$sql   = query_builder(
    'DISTINCT('.$studio_field.') as '.$studio_field.', count('.$studio_field.') as cnt ',
    $sql_studio,
    ''.$studio_field.'',
    $order
);


$result = $db->query($sql);

$rows = count($result);
if($rows <= 1) {
    JavaRefresh("genre.php?".$request_key, 0);
}
?>

<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>
<a href='genre.php?<?php echo $request_key; ?>&genre=NULL'>All</a><br>

<?php
foreach ($result as $k => $v) {
    // $v["cnt"]=1; ".$v["cnt"]."
    if ($v[$studio_field] != '') {
        echo $studio." <a href='genre.php?".$studio_field."=".$v[$studio_field]."&prev=".$studio_text."'>".$v[$studio_field].'</a> '.$v['cnt'].'<br>';
    }
}

?>
</main>
<?php
require __LAYOUT_FOOTER__;
