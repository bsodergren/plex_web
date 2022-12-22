<?php
define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));

require_once '_config.inc.php';

define('TITLE', 'View Genres');

DEFINE('__DISPLAY__', ["sort" => false , "page" => false ]);

require __LAYOUT_HEADER__;

$lib_where = $lib_where . ' AND ';

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

        $sql_studio = $lib_where.$studio_sql_query.$null;

        $request_key = $studio_key.'='.$studio_text.$null_req;

    $order = 'genre ASC';
    $sql   = query_builder(
        'DISTINCT(genre) as genre, count(genre) as cnt ',
        $sql_studio,
        'genre',
        $order
    );

                        logger('qyefasd', $sql);

                    $result = $db->query($sql);


    ?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>
<a href='files.php?<?php echo $request_key; ?>&allfiles=1'>All</a><br>

<?php
foreach ($result as $k => $v) {
    // $v["cnt"]=1; ".$v["cnt"]."
    if ($v['genre'] != '') {
        echo $studio." <a href='files.php?".$request_key.'&genre='.$v['genre']."'>".$v['genre'].'</a> '.$v['cnt'].'<br>';
    }
}

?>
 </main>
 <?php
 require __LAYOUT_FOOTER__;
