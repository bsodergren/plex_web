<?php

use UTMTemplate\Template;
use Plex\Template\Display\Display;
/**
 * plex web viewer
 */

if (!defined('APP_AUTHENTICATION')) {
    define('APP_AUTHENTICATION', false);
}

define('__MAX_PAGES_TO_SHOW__', 8);
define('ALLOWED_INACTIVITY_TIME', time() + 1 * 60);

define('SESSION_VARS',
    [
        'itemsPerPage' => '100',
        'library'      => 'Studios',
        'sort'         => 'f.added',
        'direction'    => 'DESC',
        // 'alpha' => '',
    ]);


define(
    '__AUTH_FUNCTION__',
    [
        'verify'   => 'check_logged_in_butnot_verified',
        'login'    => 'check_logged_out',
        'register' => 'check_logged_out',
        'logout'   => 'check_logged_in',
        'reset'    => 'check_logged_out',
    ]
);
const __DISPLAY_PAGES__ = [
    'genre.php'        => [
        'sort'  => 1,
        'pages' => 1,
    ],
    'list.php'        => [
        'sort'  => 1,
        'pages' => 1,
    ],
    'grid.php'         => [
        'sort'  => 1,
        'pages' => 1,
    ],
    'test.php'         => [
        'sort'  => 0,
        'pages' => 0,
    ],
    'search.php'       => [
        'sort'  => 1,
        'pages' => 1,
    ],
    // 'studioEditor.php' => [
    //     'sort'  => 1,
    //     'pages' => 1,
    // ],
    'genreEditor.php'  => [
        'sort'  => 1,
        'pages' => 1,
    ],
    'artistEditor.php' => [
        'sort'  => 1,
        'pages' => 1,
    ],
    'grid.php'     => [
        'sort'  => 1,
        'pages' => 1,
    ],
    'dupes.php'        => [
        'sort'  => 1,
        'pages' => 0,
    ],
];

Display::Random();
define('__RANDOM__', Display::$Random);

Template::$registeredCallbacks = ['\Plex\Template\Callbacks\FunctionCallback::FUNCTION_CALLBACK'=>'callback_parse_function'];