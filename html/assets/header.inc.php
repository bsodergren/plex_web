<?php
/**
 * plex web viewer
 */

use Nette\Utils\Strings;

require_once __PHP_INC_CLASS_DIR__.'/Roboloader.class.php';

$const           = get_defined_constants(true);
$query_string    = '';

$include_array   = [];

foreach ($const['user'] as $name => $value) {
    if (Strings::contains($name, '_INC_')) {
        $include_array = array_merge($include_array, RoboLoader::get_filelist($value, 'php', 1));
    } // end if
} // end foreach

global $_SESSION;

foreach ($include_array as $required_file) {
    require_once $required_file;
}

$template        = new Template();

if (!isset($_SESSION['itemsPerPage'])) {
    $_SESSION['itemsPerPage'] = 25;
}

if (isset($_REQUEST['itemsPerPage'])) {
    $_SESSION['itemsPerPage'] = $_REQUEST['itemsPerPage'];
}
unset($_REQUEST['itemsPerPage']);

// $uri['itemsPerPage'] = $_SESSION['itemsPerPage'];

if (!isset($_REQUEST['current'])) {
    $_REQUEST['current'] = '1';
} else {
    $uri['current'] = $_REQUEST['current'];
}

$currentPage     = $_REQUEST['current'];
$uri['current']  = $currentPage;
if (!isset($_SESSION['library'])) {
    $_SESSION['library'] = 'Pornhub';
}

if (isset($_REQUEST['library'])) {
    $_SESSION['library'] = $_REQUEST['library'];
}

$tag_array       = ['genre', 'artist', 'keyword'];


if (isset($_REQUEST['submit'])) {
    if ('Search' == $_REQUEST['submit']) {
        $delim                   = ',';
        $q_str[]                 = 'submit=Search';
        foreach ($tag_array as $tag) {
            if (isset($_REQUEST[$tag])) {
                $fields[] = $tag;
                $q_str[]  = 'field[]='.$tag;
                if (is_array($_REQUEST[$tag])) {
                    foreach ($_REQUEST[$tag] as $str) {
                        $q_str[] = $tag.'[]='.$str;
                    }
                }
            }
        }
        $genreStr                = implode('&', $q_str);
        $_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'].'&'.$genreStr.'&grp='.$_REQUEST['grp'];
        $_REQUEST['field']       = $fields;
    }
}
/*
$in_directory    = $_SESSION['library'];
$cache_directory = $_SESSION['library'];

if ('Studios' == $in_directory) {
    $in_directory = 'Studios';
}
*/
/*if ($in_directory == 'Home Videos') {
    $in_directory = 'HomeVideos';
}
*/

$request_key     = '';
if (!isset($_SESSION['sort'])) {
    $_SESSION['sort'] = 'm.title';
}
if (isset($_REQUEST['sort'])) {
    $_SESSION['sort'] = $_REQUEST['sort'];
}
if ('' != $_SERVER['QUERY_STRING']) {
    $query_string            = '&'.urlQuerystring($_SERVER['QUERY_STRING'], 'itemsPerPage');
    $request_string_query    = '?'.urlQuerystring($_SERVER['QUERY_STRING'], 'itemsPerPage');
    $query_string_no_current = '&'.urlQuerystring($_SERVER['QUERY_STRING'], 'current');

    $query_string_no_current = '&'.urlQuerystring($query_string_no_current, 'itemsPerPage');
    // dd([$_SERVER['QUERY_STRING'],$query_string_no_current]);
}

$urlPattern      = $_SERVER['PHP_SELF'].'?current=(:num)'.$query_string_no_current;

if (!isset($_SESSION['direction'])) {
    $_SESSION['direction'] = 'ASC';
}

if (isset($_REQUEST['direction'])) {
    if ('ASC' == $_REQUEST['direction']) {
        $_SESSION['direction'] = 'DESC';
    }

    if ('DESC' == $_REQUEST['direction']) {
        $_SESSION['direction'] = 'ASC';
    }
}


$sort_type_map = [
    'sort_types' => [
        'Studio'       => 'm.studio',
        'Sub Studio'   => 'm.substudio',
        'File size'    => 'f.filesize',
        'Artist'       => 'm.artist',
        'Title'        => 'm.title',
        'Filename'     => 'f.filename',
        'Duration'     => 'f.duration',
        'Date Added'   => 'f.added',
        'Genre'        => 'm.genre',
        ],
    'map' => [
        'm.studio'  =>'Studio'       , 
        'm.substudio'  =>'Sub Studio'   ,
        'f.filesize'  =>'File size'    ,
        'm.artist'  =>'Artist'       ,
        'm.title'  =>'Title'        ,
        'f.filename'  =>'Filename'     ,
        'f.duration' => 'Duration'     , 
        'f.added' => 'Date Added'   ,
        'm.genre' =>'Genre'        ,
    ],
];

$url_array       = [
    'url'          => $_SERVER['SCRIPT_NAME'],
    'query_string' => $query_string,
    'current'      => $_SESSION['sort'],
    'direction'    => $_SESSION['direction'],
    'sort_types' => $sort_type_map['sort_types'] ,
    // 'sort_types'   => [
    //     'Studio'       => 'm.studio',
    //     'Sub Studio'   => 'm.substudio',
    //     'File size'    => 'f.filesize',
    //     'Artist'       => 'm.artist',
    //     'Title'        => 'm.title',
    //     'Filename'     => 'f.filename',
    //     'Duration'     => 'f.duration',
    //     'Date Added'   => 'f.added',
    //     'Genre'        => 'm.genre',
    // ],
];
