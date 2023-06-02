<?php

require_once '_config.inc.php';
const TITLE = 'Home';

$sql = query_builder('studio', '', 'studio', 'studio ASC');
$result = $db->query($sql);

$all_url = 'files.php?allfiles=1';

//DEFINE('BREADCRUMB', [$in_directory => "", 'all' => $all_url]);
require __LAYOUT_HEADER__;
$index              = 1;
foreach ($result as $row => $name)
{

    $cnt = '';
    if (0 == $index % 4) {
        if ($studio_links != '') {
            $studio_box .= process_template("home/studio_box", [
                'STUDIO_LINKS' => $studio_links,
                'CLASS' => '',
            ]);
        }
        $studio_links = '';
    }

    if ($name['studio'] == '') {
        $name['studio'] = 'NULL';
        $sql_studio  = ' IS NULL';
    } else {
        $sql_studio = ' LIKE "' . $name['studio'] . '"';
    }

    $studio = urlencode($name['studio']);

    $sql = query_builder('count(video_key) as cnt', ' studio ' . $sql_studio . ' and substudio is null', 'studio', 'studio ASC');
    $rar = $db->rawQueryOne($sql);
    if (isset($rar['cnt'])) {
        $cnt = ' (' . $rar['cnt'] . ') ';
    }

    $studio_links .= process_template("home/studio_link", [
        'GET_REQUEST' =>  "studio=" . $studio,
        'NAME' =>  $name["studio"],
        'COUNT' => $cnt,
        'CLASS' => "btn btn-primary",
    ]);

    $substudio_sql = query_builder('count(substudio) as cnt, substudio', ' studio  ' . $sql_studio, 'substudio', 'substudio ASC ');
    $ss_result = $db->query($substudio_sql);

    if (count($ss_result) > 1) {
        $iindex = 1;
        foreach ($ss_result as $ssRow => $ssName) {
            if ($ssName['substudio'] != null) {
  $iindex++;
                $ssCnt    = ' (' . $ssName['cnt'] . ')';

                $substudio = urlencode($ssName['substudio']);
                $studio_links .= process_template("home/studio_link", [
                    'GET_REQUEST' =>  "substudio=" . $substudio,
                    'NAME' =>  $ssName["substudio"],
                    'COUNT' => $ssCnt,
                    'CLASS' => 'btn btn-secondary',
                ]);
                if (0 == $iindex % 4) {
                    $studio_box .= process_template("home/studio_box", [
                        'STUDIO_LINKS' => $studio_links,
                        'CLASS' => '',
                    ]);
                    $studio_links = '';
                }
              
    
            }
        }
        if ($studio_links != '') {
            $studio_box .= process_template("home/studio_box", [
                'STUDIO_LINKS' => $studio_links,
                'CLASS' => '',
            ]);
        }
        $studio_links = '';
    } else {
        if ($studio_links != '') {
            $studio_box .= process_template("home/studio_box", [
                'STUDIO_LINKS' => $studio_links,
                'CLASS' => '',
            ]);
            $studio_links = '';
        }
    } //end if

    // echo "</ul>";
    $index++;
    // }

} //end foreach

$body = process_template("home/main", ['BODY_HTML' =>  $studio_box]);
$template->render("page", ['BODY' => $body]);


require __LAYOUT_FOOTER__;
