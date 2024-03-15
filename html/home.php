<?php

use Plex\Modules\Database\PlexSql;
use Plex\Template\Display\Display;
use Plex\Template\Render;

/**
 * plex web viewer.
 */

require_once '_config.inc.php';
const TITLE = 'Home';

$subLibraries = [];
$sql = PlexSql::query_builder(Db_TABLE_VIDEO_TAGS, 'DISTINCT(subLibrary) as subLibrary ', 'library');
$result = $db->query($sql);
if (count($result) > 0) {
    foreach ($result as $_ => $value) {
        $subLibraries[] = $value['subLibrary'];
    }
}

logger('qyefasd', $sql);
$result = $db->query($sql);
$rar = $db->rawQueryOne($sql);
$sql = PlexSql::query_builder(Db_TABLE_VIDEO_TAGS, 'studio,subLibrary,count(video_key) as cnt', 'library', 'studio,subLibrary', 'studio,subLibrary ASC');
$result = $db->query($sql);

$all_url = 'list.php?allfiles=1';

// DEFINE('BREADCRUMB', [$in_directory => "", 'all' => $all_url]);
//  \Plex\Template\Layout\Header::Display();

foreach ($result as $r => $row) {
    if (null === $row['subLibrary']) {
        $row['subLibrary'] = 'Studios';
    }
    $studioArray[$row['subLibrary']][] = ['studio' => $row['studio'], 'cnt' => $row['cnt']];
}

foreach ($studioArray as $subLibrary => $studioArr) {
    $studio_box = '';

    $index = 1;

    foreach ($studioArr as $row => $sname) {
        $name = ['studio' => $sname['studio']];
        $cnt = $sname['cnt'];

        if ('' == $name['studio']) {
            $name['studio'] = 'NULL';
            $sql_studio = ' IS NULL';
        } else {
            $sql_studio = ' LIKE "'.$name['studio'].'"';
        }

        $studio = urlencode($name['studio']);

        // $sql           = PlexSql::query_builder(Db_TABLE_VIDEO_TAGS, 'count(video_key) as cnt', ' studio '.$sql_studio.' and substudio is null', 'studio', 'studio ASC');
        // $rar           = $db->rawQueryOne($sql);
        if (isset($cnt)) {
            $cnt = ' ('.$cnt.') ';
        }

        $substudio_sql = PlexSql::query_builder(Db_TABLE_VIDEO_TAGS, 'count(substudio) as cnt, substudio', ' studio  '.$sql_studio, 'substudio', 'substudio ASC ');
        $ss_result = $db->query($substudio_sql);
        if (count($ss_result) >= 2) {

            $accordian = [];
            $accordian['ACCORDIAN_ID'] = Display::RandomId('accordian_');
            $accordian['ACCORDIAN_HEADER'] = $name['studio'];

            $accordian['STUDIO_LINK'] = Render::html('home/studio/link', [
                'GET_REQUEST' => 'studio='.$studio,
                'NAME' => $name['studio'],
                'COUNT' => $cnt,
                'CLASS' => 'btn btn-secondary',
            ]);

            foreach ($ss_result as $ssRow => $ssName) {
                if (null === $ssName['substudio']) {
                    continue;
                }
                $ssCnt = ' ('.$ssName['cnt'].')';
                $substudio = urlencode($ssName['substudio']);
                $accordian['STUDIO_LINK'] .= Render::html('home/studio/link', [
                    'GET_REQUEST' => 'substudio='.$substudio,
                    'NAME' => $ssName['substudio'],
                    'COUNT' => $ssCnt,
                    'CLASS' => 'btn btn-secondary',
                ]);
            }
            $accordian['ACCORDIAN_LINKS'] = Render::html('home/studio/group', $accordian);
            $studio_links .= Render::html('home/accordian/block', $accordian);
        } else {
            $studio_links .= Render::html('home/studio/group', [
                'STUDIO_LINK' => Render::html('home/studio/link', [
                    'GET_REQUEST' => 'studio='.$studio,
                    'NAME' => $name['studio'],
                    'COUNT' => $cnt,
                    'CLASS' => 'btn btn-secondary',
                ]),
            ]);
        }
        if ('' != $studio_links) {
            $studio_box .= Render::html('home/block', [
                'STUDIO_LINKS' => $studio_links,
                'CLASS' => '',
            ]);
            $studio_links = '';
        }
        // echo "</ul>";
        ++$index;
        // }
    }

    $studio_html .= Render::html('home/studio/lib', [
        'STUDIO_BOX_HTML' => $studio_box,
        'LIBRARY_NAME' => $subLibrary]);
} // end foreach

Render::Display($studio_html);
