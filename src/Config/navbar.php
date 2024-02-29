<?php

$navigation_link_array = [
    /*

        'View'    => [
            'Artist' => 'view/artist.php',
            'Studio' => 'view/studio.php',
            'genre'  => 'view/genre.php',


        ],
    ],

        'Missing' => [
            'Titles' => 'missing/title.php',
            'Artist' => 'missing/artist.php',
            'Genre'  => 'missing/genre.php',
            'Studio' => 'missing/studio.php',
        ],

    ],
*/

    'home' => [
        'url' => 'home.php',
        'text' => 'Home',
        'icon' => 'home',
        'studio' => false,
    ],
    'files' => [
        'url' => 'files.php',
        'text' => 'List  Display',
        'icon' => 'list',
        'studio' => true,
    ],
    'gridview' => [
        'url' => 'gridview.php',
        'text' => 'Grid Display',
        'icon' => 'grid',
       
        'studio' => true,
    ],

    'playlist' => [
        'url' => 'playlist.php',
        'text' => 'Playlist Display',
        'icon' => 'playlist',
        'studio' => true,
    ],

    'tag' => [
        'url' => 'tags.php',
        'text' => 'Tags',
        'icon' => '',
        'studio' => false,
    ],
    'artist' => [
        'url' => 'artist.php',
        'text' => 'Artist Page',
        'icon' => '',
        'studio' => false,
    ],

    'search' => [
        'url' => 'search.php',
        'text' => 'Search',
        'icon' => '',
        'studio' => false,
    ],

    'dropdown' => [
        'Extra' => [
            'smartlist' => 'smartpl.php',
            'Duplicates' => 'dupes.php',
            'Divider_1'=>1,

            'artistEditor' => 'Config/artistEditor.php',
            'studioEditor' => 'Config/studioEditor.php',
            'genreEditor' => 'Config/genreEditor.php',
            'Divider_2'=>1,
            'wordMap' => 'editor.php',

            'File Browser' => 'filebrowser.php',
            'test' => 'test.php',
            'Divider_3'=>1,
            'refresh' => 'process.php?action=refresh',
            'settings' => 'settings.php',
        ],
    ],
];
