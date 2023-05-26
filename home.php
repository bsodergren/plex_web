<?php

require_once '_config.inc.php';
const TITLE = 'Home';

$sql = query_builder('studio', '', 'studio', 'studio ASC');
$result = $db->query($sql);


$sql     = query_builder('DISTINCT(library) as library ');
$result2 = $db->query($sql);


$all_url = 'genre.php?allfiles=1';

DEFINE('BREADCRUMB', [$in_directory => "", 'all' => $all_url]);
require __LAYOUT_HEADER__;

$json_array['menu'] = [];
$index              = 1;
foreach ($result as $k => $v) {

    if (0 == $index % 4) {
        $studio_box .= process_template("home/studio_box", [
            'STUDIO_LINKS' => $studio_links,
            'CLASS' => '',
        ]);
        $studio_links = '';
    }
    // if ($v["studio"] != "")
    // {
    if ($v['studio'] == '') {
        $v['studio'] = 'NULL';
        $sql_studio  = ' IS NULL';
    } else {
        $sql_studio = ' LIKE "' . $v['studio'] . '"';
    }

    $sql = query_builder('count(video_key) as cnt', ' studio ' . $sql_studio . ' and substudio is null', 'studio', 'studio ASC');
    $rar = $db->rawQueryOne($sql);
    $cnt = '';
    if (isset($rar['cnt'])) {
        $cnt = ' (' . $rar['cnt'] . ') ';
    }

    $sql2 = query_builder('count(substudio) as cnt, substudio', ' studio  ' . $sql_studio, 'substudio', 'substudio ASC ');

    $alt_result = $db->query($sql2);

    $link = '';

    $studio = urlencode($v['studio']);


    $studio_links .= process_template("home/studio_link", [
        'GET_REQUEST' =>  "studio=" . $studio,
        'NAME' =>  $v["studio"],
        'COUNT' => $cnt,
        'CLASS' => "btn btn-primary",
    ]);

    if (count($alt_result) > 1) {
        foreach ($alt_result as $k_a => $v_a) {
            if ($v_a['substudio'] != null) {

                $cntv_a    = ' (' . $v_a['cnt'] . ')';

                $substudio = urlencode($v_a['substudio']);
                $studio_links .= process_template("home/studio_link", [
                    'GET_REQUEST' =>  "substudio=" . $substudio,
                    'NAME' =>  $v_a["substudio"],
                    'COUNT' => $cntv_a,
                    'CLASS' => 'btn btn-secondary',
                ]);
                // echo "<li><a href='genre.php?substudio=" . $substudio . "'>" . $v_a["substudio"] . "</a>" . $cntv_a . "<br>";
                $index++;
                if (0 == $index % 4) {
                    $studio_box .= process_template("home/studio_box", [
                        'STUDIO_LINKS' => $studio_links,
                        'CLASS' => '',
                    ]);
                    $studio_links = '';
                }
            }
        }
    } //end if

    // echo "</ul>";
    $index++;
    // }

} //end foreach

echo process_template("home/main", ['BODY_HTML' =>  $studio_box]);


require __LAYOUT_FOOTER__;
