<?php
/**
 * plex web viewer
 */

use Plex\Template\Rain;

$_REQUEST['itemsPerPage'] = 25;
$_REQUEST['current']      = '3';
require_once '_config.inc.php';
define('TITLE', 'Test Page');

$fileinfo                 = new FileListing($_REQUEST, $currentPage, $urlPattern);

[$results,$pageObj,$uri]  = $fileinfo->getVideoArray();
foreach ($results as $k => $videoDetails) {
    foreach ($videoDetails as $key => $value) {
        if ('artist' == $key
        || 'genre' == $key
        || 'keyword' == $key
        || 'studio' == $key
        ) {
            $videoArray['videos'][$k][$key] = explode(',', $value);
        } else {
            $videoArray['videos'][$k][$key] = $value;
        }
    }
}

$t                        = new Rain();
$tpl                      = $t->init();
$tpl->assign($videoArray);
$tpl->draw('body');

exit;

// define('BREADCRUMB', ['home' => "home.php"]);
include __LAYOUT_HEADER__;

$db->join(Db_TABLE_VIDEO_TAGS.' m', 'm.video_key=f.video_key', 'INNER');
$db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.studio', 'Brazzers');
$db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.library', 'Pornhub');
$db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.genre', '%MMF%', 'like');
$db->orderBy('m.title', 'asc');
$products                 = $db->get(Db_TABLE_VIDEO_FILE.' f', [0, 5], 'm.video_key,thumbnail,m.title,m.artist,m.genre,m.studio,m.keyword,m.substudio,f.filename ,f.fullpath,m.library,f.filesize');
echo $db->getlastquery();

print_r2($products);

exit;
$sql                      = 'select artist from '.Db_TABLE_VIDEO_FILE;
$sql                      = $sql." WHERE library = '".$_SESSION['library']."' and artist is not null";
$results                  = $db->query($sql);
$AristArray               = [];

function compareArtist(&$array, $artist)
{
    $keyName = strtolower(str_replace('.', '-', $artist));
    $keyName = strtolower(str_replace(' ', '_', $keyName));

    if (array_key_exists($keyName, $array)) {
        ++$array[$keyName];
    } else {
        $array[$keyName] = 1;
    }
}

foreach ($results as $k => $value) {
    if (str_contains($value['artist'], ',')) {
        $name_arr = explode(',', $value['artist']);
        foreach ($names_arr as $name) {
            compareArtist($AristArray, $name);
        }
    } else {
        compareArtist($AristArray, $value['artist']);
    }
}

foreach ($AristArray as $artist => $num) {
    $sortedArray[$num][] = $artist;
}

$array                    = asort($sortedArray);
$artist_html              = '';
foreach ($sortedArray as $num => $artistArray) {
    $artist_box                 = [];
    $link_array                 = [];

    $artist_box['COUNT_HTML']   = process_template('test/artist_count', ['ARTIST_COUNT' => $num]);
    $artist_links               = '';

    // foreach($artistArray as $artist)
    // {

    //    //$artist_links .= process_template("test/artist_link",['ARTIST'=>$artist,'ARTIST_NAME'=>$name]);
    //    //$artist_links .= keyword_cloud($name,'artist');
    //  //dump( [ $num ,$artist]);
    // }
    $field                      = 'artist';
    $search_url                 = 'search.php?field='.$field.'&query=';

    foreach ($artistArray as $k => $artist) {
        $name         = strtolower(str_replace('-', '.', $artist));
        $name         = strtolower(str_replace('_', ' ', $name));
        $link_array[] = process_template(
            'filelist/search_link',
            [
                'KEY'      => $field,
                'QUERY'    => urlencode($name),
                'URL_TEXT' => $name,
                'CLASS'    => ' class="badge fs-6 blueTable-thead" ',
            ]
        );
    }

    $artist_links               = implode('  ', $link_array);
    // dd($link_array);
    $artist_box['ARTIST_LINKS'] = $artist_links;

    $artist_html .= process_template('test/artist_box', $artist_box);
}

$PARAMS['ARTIST_HTML']    = $artist_html;
Template::echo('cloud/main', ['TAG_CLOUD_HTML' => $artist_html]);
// Template::echo("test/main",$PARAMS);

include __LAYOUT_FOOTER__;
