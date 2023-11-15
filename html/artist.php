<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';
define('TITLE', 'artist Page');

// define('BREADCRUMB', ['home' => "home.php"]);
include __LAYOUT_HEADER__;

$sql                   = 'select artist from '.Db_TABLE_FILEDB;
$sql                   = $sql." WHERE library = '".$_SESSION['library']."' and (artist is not null and artist != 'Missing')";
$results               = $db->query($sql);
$AristArray            = [];
// $sortedArray[0]      = [];

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

$array                 = krsort($sortedArray, \SORT_NUMERIC);
// $array= ksort($sortedArray,SORT_NUMERIC);
$artist_html           = '';
foreach ($sortedArray as $num => $artistArray) {
    $artist_box                 = [];
    $link_array                 = [];

    sort($artistArray);
    $artist_box['COUNT_HTML']   = process_template('artist/artist_count', ['ARTIST_COUNT' => $num]);
    $artist_links               = '';

    // foreach($artistArray as $artist)
    // {

    //    //$artist_links .= process_template("artist/artist_link",['ARTIST'=>$artist,'ARTIST_NAME'=>$name]);
    //    //$artist_links .= keyword_cloud($name,'artist');
    //  //dump( [ $num ,$artist]);
    // }
    $field                      = 'artist';
    $search_url                 = 'search.php?field='.$field.'&query=';
    // $last_letter = '';
    foreach ($artistArray as $k => $artist) {
        $letter       = substr($artist, 0, 1);
        if (!isset($last_letter)) {
            $last_letter = $letter;
        }
        if ($letter != $last_letter) {
            $last_letter  = $letter;
            $link_array[] = '</div><div class="d-flex flex-wrap mt-2">';
        }
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
    unset($last_letter);
    $artist_links               = implode('  ', $link_array);
    // dd($link_array);
    $artist_box['ARTIST_LINKS'] = $artist_links;

    $artist_html .= process_template('artist/artist_box', $artist_box);
}
$params['ARTIST_HTML'] = $artist_html;

$sql                   = 'select * from '.Db_TABLE_FILEDB;
$sql                   = $sql." WHERE library = '".$_SESSION['library']."' and (artist is  null or artist = 'Missing')";
$results               = $db->query($sql);
foreach ($results as $num => $artistArray) {
    $title     = $artistArray['title'];
    $id        = $artistArray['id'];
    $thumbnail = $artistArray['thumbnail'];
    if ('' == $artistArray['title']) {
        $title = $artistArray['filename'];
    }
    $params['THUMBNAIL_HTML'] .= process_template(
        'artist/artist_thumbnail',
        [
            'THUMBNAIL' => __URL_HOME__.$thumbnail,
            'FILE_ID'   => $id,
            'TITLE'     => $title,
        ]
    );
    //  dd($artistArray);
}

echo process_template('artist/cloud', $params);
// echo process_template("artist/main",$PARAMS);

include __LAYOUT_FOOTER__;
