<?php

require_once '_config.inc.php';


$results               = (new PlexSql())->getArtists();
$AristArray            = [];
// $sortedArray[0]      = [];
function compareArtist(&$array, $artist)
{
    $keyName = strtolower(str_replace('.', '-', $artist));
    $keyName = strtolower(str_replace(' ', ' ', $keyName));
$keyName = ucwords($keyName);
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
    $sortedArray[] = $artist;
}
$array = array_unique($sortedArray);
sort($array,SORT_REGULAR);
//$array                 = rsort());
foreach($array as $k => $value){
    $list[] = '{ "text": "'.$value.'", "value": "'.$value.'" }';// . '<br>';
    
}

echo '['. implode(",",$list) . ']';
