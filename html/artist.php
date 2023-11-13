<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';
define('TITLE', 'Test Page');

// define('BREADCRUMB', ['home' => "home.php"]);
include __LAYOUT_HEADER__;

$sql             = 'select artist from '.Db_TABLE_FILEDB;
$sql             = $sql." WHERE library = '".$_SESSION['library']."' and artist is not null";
$results         = $db->query($sql);
$AristArray      = [];

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

$array= asort($sortedArray);
$artist_html = '';
foreach($sortedArray as $num => $artistArray)
{
    $artist_box = [];
    $link_array = [];


    $artist_box['COUNT_HTML'] = process_template("test/artist_count",['ARTIST_COUNT'=>$num]);
    $artist_links = '';

    // foreach($artistArray as $artist)
    // {
        
    //    //$artist_links .= process_template("test/artist_link",['ARTIST'=>$artist,'ARTIST_NAME'=>$name]);
    //    //$artist_links .= keyword_cloud($name,'artist');
    //  //dump( [ $num ,$artist]);
    // }
    $field = 'artist';
    $search_url = 'search.php?field='.$field.'&query=';

    foreach ($artistArray as $k => $artist) {
        $name = strtolower(str_replace('-', '.', $artist));
        $name = strtolower(str_replace('_', ' ', $name));
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

    $artist_links  = implode('  ', $link_array);
//dd($link_array);
    $artist_box['ARTIST_LINKS'] = $artist_links;

    $artist_html .= process_template("test/artist_box",$artist_box);
  

}

$PARAMS['ARTIST_HTML'] = $artist_html;
echo process_template('cloud/main', ['TAG_CLOUD_HTML' => $artist_html]);
//echo process_template("test/main",$PARAMS);

include __LAYOUT_FOOTER__;
