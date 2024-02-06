<?php
/**
 * plex web viewer
 */

use Plex\Core\RoboLoader;
global $_SESSION;

$include_array =  RoboLoader::get_filelist(__PHP_INC_CORE_DIR__, 'php', 1);

foreach ($include_array as $required_file) {
    require_once $required_file;
}


foreach (SESSION_VARS as $key => $default) {
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = $default;
    }
    if ('direction' == $key && '' == $_SESSION[$key]) {
        $_SESSION['direction'] = 'DESC';
    }

    if (isset($_REQUEST[$key])) {
        $_SESSION[$key] = $_REQUEST[$key];
        if ('direction' == $key) {
            if ('DESC' == $_REQUEST['direction']) {
                $_SESSION['direction'] = 'ASC';
            } elseif ('ASC' == $_REQUEST['direction']) {
                $_SESSION['direction'] = 'DESC';
            } else {
                $_SESSION['direction'] = 'DESC';
            }
        }
    }
}

unset($_REQUEST['itemsPerPage']);
if (isset($_REQUEST['alpha'])) {
    // $_REQUEST['alpha'] = '1';
    // } else {
    $uri['alpha'] = $_REQUEST['alpha'];
}

if (!isset($_REQUEST['current'])) {
    $_REQUEST['current'] = '1';
} else {
    $uri['current'] = $_REQUEST['current'];
}

$currentPage    = $_REQUEST['current'];
$uri['current'] = $currentPage;

$tag_array      = ['genre', 'artist', 'keyword'];

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

$request_key    = '';

if ('' != $_SERVER['QUERY_STRING']) {
    $query_string            = '&'.urlQuerystring($_SERVER['QUERY_STRING'], 'itemsPerPage');
    $request_string_query    = '?'.urlQuerystring($_SERVER['QUERY_STRING'], 'itemsPerPage');
    $query_string_no_current = '&'.urlQuerystring($_SERVER['QUERY_STRING'], 'current');

    $query_string_no_current = '&'.urlQuerystring($query_string_no_current, 'itemsPerPage');
    // dd([$_SERVER['QUERY_STRING'],$query_string_no_current]);
}

$urlPattern     = $_SERVER['PHP_SELF'].'?current=(:num)'.$query_string_no_current;

$sort_type_map  = [
    'sort_types' => [
        'Studio'     => 'm.studio',
        'Sub Studio' => 'm.substudio',
        'Title'      => 'm.title',
        'Genre'      => 'm.genre',

        'Artist'     => 'm.artist',
        'Filename'   => 'f.filename',

        'File size'  => 'f.filesize',
        'Duration'   => 'f.duration',
        'Date Added' => 'f.added',
        'Rating'     => 'f.rating',
    ],
    'map'        => [
        'f.rating'    => 'Rating',
        'm.studio'    => 'Studio',
        'm.substudio' => 'Sub Studio',
        'f.filesize'  => 'File size',
        'm.artist'    => 'Artist',
        'm.title'     => 'Title',
        'f.filename'  => 'Filename',
        'f.duration'  => 'Duration',
        'f.added'     => 'Date Added',
        'm.genre'     => 'Genre',
    ],
];

$url_array      = [
    'url'          => $_SERVER['SCRIPT_NAME'],
    'sortDefault' => 'm.title',
    'query_string' => $query_string,
    'current'      => $_SESSION['sort'],
    'direction'    => $_SESSION['direction'],
    'sort_types'   => $sort_type_map['sort_types'],

];
