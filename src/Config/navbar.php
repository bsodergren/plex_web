<?php

$navigation_link_array = [
    /*
    'dropdown' => [
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
        'secure' => false,
        'js' => false,
        'icon' => 'home',
    ],
    'files' => [
        'url' => 'files.php',
        'text' => 'List  Display',
        'secure' => false,
        'js' => false,
        'studio' => true,
    ],
    'gridview' => [
        'url' => 'gridview.php',
        'text' => 'Grid Display',
        'secure' => false,
        'js' => false,
        'studio' => true,
    ],

    'playlist' => [
        'url' => 'playlist.php',
        'text' => 'Playlist Display',
        'icon' => 'playlist',
        'secure' => false,
        'js' => false,
    ],
    'smartlist' => [
        'url' => 'smartpl.php',
        'text' => 'Smart List',
        'secure' => false,
        'js' => false,
    ],
    'tag' => [
        'url' => 'tags.php',
        'text' => 'Tags',
        'secure' => false,
        'js' => false,
    ],
    'artist' => [
        'url' => 'artist.php',
        'text' => 'Artist Page',
        'secure' => false,
        'js' => false,
    ],

    'search' => [
        'url' => 'search.php',
        'text' => 'Search',
        'secure' => false,
        'js' => false,
    ],
    'dups' => [
        'url' => 'dupes.php',
        'text' => 'Duplicates',
        'secure' => false,
        'js' => false,
    ],

    // 'artistEditor' => [
    //     'url'    => 'Config/artistEditor.php',
    //     'text'   => 'Config artists',
    //     'secure' => false,
    //     'js'     => false,
    // ],
    // 'studioEditor' => [
    //     'url'    => 'Config/studioEditor.php',
    //     'text'   => 'Config Studio',
    //     'secure' => false,
    //     'js'     => false,
    // ],
    'genreEditor' => [
        'url' => 'Config/genreEditor.php',
        'text' => 'Config Genre',
        'secure' => false,
        'js' => false,
    ],
    'wordMap' => [
        'url' => 'editor.php',
        'text' => 'Word Map',
        'secure' => false,
        'js' => false,
    ],

    /*
    'File Browser' => [
        'url'    => 'filebrowser.php',
        'text'   => 'File Browser',
        'secure' => false,
        'js'     => false,
    ],
    'test'     => [
        'url'    => 'test.php',
        'text'   => 'Test Page',
        'secure' => false,
        'js'     => false,
    ],
    */

    'refresh' => [
        'url' => 'process.php?action=refresh',
        'text' => 'Refresh',
        'secure' => false,
        'js' => false,
    ],

    'settings' => [
        'url' => 'settings.php',
        'text' => 'Settings',
        'secure' => false,
        'js' => false,
    ],
    'logout' => [
        'url' => 'logout.php',
        'text' => 'Log Out',
        'secure' => true,
        //        'js'     => ' onclick="logout();"',
        'js' => false,
    ],
];
