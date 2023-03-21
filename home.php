<?php

require_once '_config.inc.php';
const TITLE = 'Home';

$sql = query_builder('studio','', 'studio', 'studio ASC');

$result = $db->query($sql);

$sql     = query_builder('DISTINCT(library) as library ');
$result2 = $db->query($sql);

$all_url = 'genre.php?allfiles=1';

DEFINE('BREADCRUMB', ['home' => "", 'all' => $all_url]);
require __LAYOUT_HEADER__;

?>
<main role="main" class="container">
<ul id="menu" class="list">
<?php
$json_array['menu'] = [];
$index              = 0;
$sidx               = 0;
foreach ($result as $k => $v) {
    // if ($v["studio"] != "")
    // {
    if ($v['studio'] == '') {
        $v['studio'] = 'NULL';
        $sql_studio  = ' IS NULL';
    } else {
        $sql_studio = ' LIKE "'.$v['studio'].'"';
    }

    $sql = query_builder('count(video_key) as cnt', ' studio '.$sql_studio.' and substudio is null', 'studio', 'studio ASC');

    $rar = $db->rawQueryOne($sql);
    $cnt = '';
    if (isset($rar['cnt'])) {
        $cnt = ' ('.$rar['cnt'].') ';
    }

    $sql = query_builder('count(substudio) as cnt, substudio', ' studio  '.$sql_studio, 'substudio', 'substudio ASC ');

    $alt_result = $db->query($sql);

    $link = '';
    
    $studio = urlencode($v['studio']);

    $json_array['menu'][$index]['name'] = $v['studio'].$cnt;
    $json_array['menu'][$index]['link'] = 'genre.php?studio='.$studio.$link;

    if (count($alt_result) > 1) {
        $json_array['menu'][$index]['sub'] = [];
        $sidx = 0;
        // echo "<ul>";
        foreach ($alt_result as $k_a => $v_a) {
            if ($v_a['substudio'] != null) {
                $json_array['menu'][$index]['sub'][$sidx] = [];

                $cntv_a    = ' ('.$v_a['cnt'].')';
        
                $substudio = urlencode($v_a['substudio']);

                $json_array['menu'][$index]['sub'][$sidx]['name'] = $v_a['substudio'].$cntv_a;
                $json_array['menu'][$index]['sub'][$sidx]['link'] = 'genre.php?substudio='.$substudio;
                $json_array['menu'][$index]['sub'][$sidx]['sub']  = null;

                $sidx++;

                // echo "<li><a href='genre.php?substudio=" . $substudio . "'>" . $v_a["substudio"] . "</a>" . $cntv_a . "<br>";
            }
        }
    } else {
        $json_array['menu'][$index]['sub'] = null;
    }//end if

    // echo "</ul>";
    $index++;
    // }
}//end foreach
?>
</ul>
</main>
<?php
require __LAYOUT_FOOTER__;
