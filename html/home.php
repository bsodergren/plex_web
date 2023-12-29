<?php

use Plex\Database\PlexSql;
use Plex\Template\Template;

/**
 * plex web viewer
 */

require_once '_config.inc.php';
const TITLE   = 'Home';

$subLibraries = [];
$sql          = PlexSql::query_builder(Db_TABLE_VIDEO_TAGS, 'DISTINCT(subLibrary) as subLibrary ', 'library');
$result       = $db->query($sql);
// dump($result);
if (count($result) > 0) {
    foreach ($result as $_ => $value) {
        $subLibraries[] = $value['subLibrary'];
    }
}

logger('qyefasd', $sql);
$result       = $db->query($sql);
$rar          = $db->rawQueryOne($sql);
$sql          = PlexSql::query_builder(Db_TABLE_VIDEO_TAGS, 'studio,subLibrary,count(video_key) as cnt', 'library', 'studio,subLibrary', 'studio,subLibrary ASC');
$result       = $db->query($sql);

$all_url      = 'files.php?allfiles=1';

// DEFINE('BREADCRUMB', [$in_directory => "", 'all' => $all_url]);
require __LAYOUT_HEADER__;

foreach ($result as $r => $row) {
    if (null === $row['subLibrary']) {
        $row['subLibrary'] = 'Studios';
    }
    $studioArray[$row['subLibrary']][] = ['studio' => $row['studio'], 'cnt' => $row['cnt']];
}

// dd($studioArray);
foreach ($studioArray as $subLibrary => $studioArr) {
    $studio_box = '';

    $index      = 1;

    foreach ($studioArr as $row => $sname) {
        $name          = ['studio' => $sname['studio']];
        $cnt           = $sname['cnt'];
        if (0 == $index % 4) {
            if ('' != $studio_links) {
                $studio_box .= Template::GetHTML('home/studio_box', [
                    'STUDIO_LINKS' => $studio_links,
                    'CLASS'        => '',
                ]);
            }
            $studio_links = '';
        }

        if ('' == $name['studio']) {
            $name['studio'] = 'NULL';
            $sql_studio     = ' IS NULL';
        } else {
            $sql_studio = ' LIKE "'.$name['studio'].'"';
        }

        $studio        = urlencode($name['studio']);

        // $sql           = query_builder(Db_TABLE_VIDEO_TAGS, 'count(video_key) as cnt', ' studio '.$sql_studio.' and substudio is null', 'studio', 'studio ASC');
        // $rar           = $db->rawQueryOne($sql);
        if (isset($cnt)) {
            $cnt = ' ('.$cnt.') ';
        }

        $studio_links .= Template::GetHTML('home/studio_link', [
            'GET_REQUEST' => 'studio='.$studio,
            'NAME'        => $name['studio'],
            'COUNT'       => $cnt,
            'CLASS'       => 'btn btn-primary',
        ]);

        $substudio_sql = PlexSql::query_builder(Db_TABLE_VIDEO_TAGS, 'count(substudio) as cnt, substudio', ' studio  '.$sql_studio, 'substudio', 'substudio ASC ');
        $ss_result     = $db->query($substudio_sql);

        if (count($ss_result) >= 1) {
            $iindex       = 1;
            foreach ($ss_result as $ssRow => $ssName) {
                if (null != $ssName['substudio']) {
                    ++$iindex;
                    $ssCnt     = ' ('.$ssName['cnt'].')';

                    $substudio = urlencode($ssName['substudio']);
                    $studio_links .= Template::GetHTML('home/studio_link', [
                        'GET_REQUEST' => 'substudio='.$substudio,
                        'NAME'        => $ssName['substudio'],
                        'COUNT'       => $ssCnt,
                        'CLASS'       => 'btn btn-secondary',
                    ]);
                    if (0 == $iindex % 8) {
                        $studio_box .= Template::GetHTML('home/studio_box', [
                            'STUDIO_LINKS' => $studio_links,
                            'CLASS'        => '',
                        ]);
                        $studio_links = '';
                    }
                }
            }
            if ('' != $studio_links) {
                $studio_box .= Template::GetHTML('home/studio_box', [
                    'STUDIO_LINKS' => $studio_links,
                    'CLASS'        => '',
                ]);
            }
            $studio_links = '';
        } else {
            if ('' != $studio_links) {
                $studio_box .= Template::GetHTML('home/studio_box', [
                    'STUDIO_LINKS' => $studio_links,
                    'CLASS'        => '',
                ]);
                $studio_links = '';
            }
        } // end if

        // echo "</ul>";
        ++$index;
        // }
    }

    $studio_html .= Template::GetHTML('home/studio_lib', [
        'STUDIO_BOX_HTML' => $studio_box,
        'LIBRARY_NAME'    => $subLibrary]);
} // end foreach

$body         = Template::GetHTML('home/main', ['BODY_HTML' => $studio_html]);
Template::echo('base/page', ['BODY' => $body]);

require __LAYOUT_FOOTER__;
