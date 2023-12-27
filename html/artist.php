<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';
define('TITLE', 'artist Page');

// define('BREADCRUMB', ['home' => "home.php"]);
include __LAYOUT_HEADER__;

$results               = (new PlexSql())->getArtists();

$VideoDisplay          = new VideoDisplay();
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

$sql                   = 'select m.title,f.id,f.thumbnail,f.filename from '.Db_TABLE_VIDEO_TAGS.' as m, '.Db_TABLE_VIDEO_FILE.' as f';
$sql                   = $sql." WHERE m.library = '".$_SESSION['library']."' and (m.artist is  null or m.artist = 'Missing' ) and (f.video_key = m.video_key)";
$results               = $db->query($sql);
foreach ($results as $num => $artistArray) {
    $title     = $artistArray['title'];
    $id        = $artistArray['id'];
    $thumbnail = $artistArray['thumbnail'];
    $titleBg   = '';
    if ('' == $artistArray['title']) {
        $title   = str_replace('_', ' ', $artistArray['filename']);
        $titleBg = ' bg-info ';
    }
    $params['THUMBNAIL_HTML'] .= process_template(
        'artist/artist_thumbnail',
        [
            'MISSING_TITLE_BG' => $titleBg,
            'THUMBNAIL'        => $VideoDisplay->fileThumbnail($id),
            'FILE_ID'          => $id,
            'TITLE'            => $title,
        ]
    );
    //  dd($artistArray);
}

Template::echo('artist/cloud', $params);
// Template::echo("artist/main",$PARAMS);

include __LAYOUT_FOOTER__;
