<?php

$studio_ignore = [
#    'teamskeet_selects',
    'teamskeet_extras',
];


$studio_pattern = [


    'bang' => [
        'title'  => [
            'pattern' => '/([a-zA-Z_\-0-9]{1,})\.([0-9pk]{1,}\.mp4)/i',
            'group' => 1,
            'delimeter' => '-',
        ],
    ],



    'teamskeet_selects' => [
        'title'  => [
            'pattern' => '/(teamskeetselects)\_([a-zA-Z_]{1,})[0-9]?\_full.*[0-9]{1,4}.*\.mp4/i',
            'group' => 2
        ],
    ],

    'mommy_blows_best' => [
        'title' => [
            'pattern' => '/(([a-zA-Z0-9\-]+))\_s[0-9]{2,3}\_(.*)\_[0-9]{1,4}(p|k)\.mp4/i',
            'group' => 2
         ],
        ],
    'blowpass' => [
        'title'  => '/(([a-zA-Z0-9\-]+))\_s[0-9]{2,3}\_(.*)\_[0-9]{1,4}(p|k)\.mp4/i',
        'artist' => [
            'pattern'   => '/(([a-zA-Z0-9\-]+))\_s[0-9]{2,3}\_(.*)\_[0-9]{1,4}(p|k)/i',
            'delimeter' => '_',
            'group'     => 3,
        ],
    ],

    'nubiles' => [
        'title' => [
            'pattern' => '/(ns|mts|dg|tft|ssc|mts|mfp|net|phd)\_(.*)\_[0-9]{3,4}/',
            'group' => 2,
            'episode_pattern' => '/([s0-9]{2,4})([e0-9]{2,4}) ?([a-zA-Z\_0-9\s]*)/i',
        ],
    ],
    'bad_milfs' => [
        'artist' => [
            'pattern'   => '/[a-z]{1,}\_([a-zA-Z_]{1,})[0-9]?\_full.*[0-9]{1,4}.*\.mp4/',
            'delimeter' => '_and_',
            'group'     => 1,
        ],
    ],
    'perv_mom' => [
        'artist' => [
            'pattern'   => '/[a-z]{1,}\_([a-zA-Z_]{1,})[0-9]?\_full.*[0-9]{1,4}.*\.mp4/',
            'delimeter' => '_and_',
            'group'     => 1,
        ],
    ], 
    'step_siblings' => [
        'artist' => [
            'pattern'   => '/[a-z]{1,}\_([a-zA-Z_]{1,})[0-9]?\_full.*[0-9]{1,4}.*\.mp4/',
            'delimeter' => '_and_',
            'group'     => 1,
        ],
    ], 
    'teamskeet' => [
        'artist' => [
            'pattern'   => '/[a-z]{1,}\_([a-zA-Z_]{1,})[0-9]?\_full.*[0-9]{1,4}.*\.mp4/',
            'delimeter' => '_and_',
            'group'     => 1,
        ],
    ], 
    'brazzers' => [
        'artist' => [
            'pattern'   => '/([a-zA-Z]{1,4})\_([a-zA-Z\_]*)\_[a-z]{2}[0-9]{1,10}/',
            'delimeter' => '_',
            'group'     => 2,
        ],
    ],



    'pornworld' => [
        'artist' => [
            'pattern'   => '/(([A-Z0-9]+))\_([a-zA-Z_]+)\_(HD|WEB|[0-9PK]+)/i',
            'delimeter' => '_and|',
            'group'     => 3,
        ],
    ],
    'ddf' => [
        'title'  => [
            'pattern' => '/.*\_-\_((.*))(\-[0-9]{3,5}?)\.mp4/i',
            'group'   => 1,
        ],

        'artist' => [
            'pattern'   => '/([a-zA-Z_é\.]*)\_-\_(.*)(\-[0-9]{3,5}?)\.mp4/i',
            'delimeter' => '_and_',
            'group'     => 1,
        ],
    ],

    'brazzers' => [
        'title'  => [
            'pattern' => '/.*\_-\_((.*))(\-[0-9]{3,5}?)\.mp4/i',
            'group'   => 1,
        ],

        'artist' => [
            'pattern'   => '/([a-zA-Z]{1,4})\_([a-zA-Z\_]*)\_[a-z]{2}[0-9]{1,10}/i',
            'delimeter' => '_',
            'group'     => 2,
        ],
    ]


];
