<?php

use Plex\Template\Render;
use Plex\Template\Display\Display;
use Plex\Template\Functions\Functions;

define('__SHOW_SORT__', true);
define('TITLE', 'Home');
define('NONAVBAR', true);
define('VIDEOINFO', true);
define('SHOW_RATING', true);
require_once '_config.inc.php';

use Spatie\Url\Url;

$url = Url::fromString($_SERVER['REQUEST_URI']);
$newLink = Url::create()
->withScheme($_SERVER['REQUEST_SCHEME'])
->withHost($_SERVER['SERVER_NAME'])
->withPath($url->getpath())
->withQuery($url->getQuery());

dump($url->getQuery());
// echo $url->getPath();

echo $newLink;
//$url->withoutQueryParameter('Studio'); // 'https://spatie.be/opensource?utm_source=github'
//echo $url->withQueryParameters(['utm_campaign' => 'packages']); 


$test_link = [
    'home' => [
        'url' => 'home.php',
        'text' => 'Home',
        'icon' => 'home',
        'dropdown' => false,
    ],

    'list' => [
        'url' => 'list.php',
        'text' => 'List  Display',
        'icon' => 'list',
        'dropdown' => false,
    ],

    'grid' => [
        'url' => 'grid.php',
        'text' => 'Grid Display',
        'icon' => 'grid',
        'dropdown' => false,
    ],

    'playlist' => [
        'url' => 'playlist.php',
        'text' => 'Playlist Display',
        'icon' => 'playlist',
        'dropdown' => false,
    ],

    'tag' => [
        'url' => 'tags.php',
        'text' => 'Tags',
        'icon' => '',
        'dropdown' => false,
    ],
    'artist' => [
        'url' => 'artist.php',
        'text' => 'Artist Page',
        'icon' => '',
        'dropdown' => false,
    ],

    'search' => [
        'url' => 'search.php',
        'text' => 'Search',
        'icon' => '',
        'dropdown' => false,
    ],

    'smartlist' => [
        'url' =>'smartpl.php',
        'text' => 'Smart List',
        'icon' => '',
        'dropdown' => true,
    ],
    'test' => [
        'url' => 'test.php',
        'text' => 'Test',
        'icon' => '',
        'dropdown' => true,
    ],
    'duplicates' => [
        'url' => 'dupes.php',
        'text' => 'Duplicates',
        'icon' => '',
        'dropdown' => true,
    ],
    'settings' => [
        'url' =>'settings.php',
        'text' => 'Settings',
        'icon' => '',
        'dropdown' => true,
    ],

    // 'dropdown' => [
    //     'Extra' => [
    //         'smartlist' => 'smartpl.php',
    //         'Duplicates' => 'dupes.php',
    //         'Divider_1'=>1,

    //         'artistEditor' => 'Config/artistEditor.php',
    //         'studioEditor' => 'Config/studioEditor.php',
    //         'genreEditor' => 'Config/genreEditor.php',
    //         'Divider_2'=>1,
    //         'wordMap' => 'editor.php',

    //         'File Browser' => 'filebrowser.php',
    //         'test' => 'test.php',
    //         'Divider_3'=>1,
    //         'refresh' => 'process.php?action=refresh',
    //         'settings' => 'settings.php',
    //     ],
    // ],
];


 function navbar_links()
{
    $html = '';
    $dropdown_html = '';
    global $test_link;
    global $_REQUEST;

   

    foreach ($test_link as $name => $link_array) {
        utmdump($link_array);
        $is_active = '';
        if ($link_array['dropdown'] == true) {
            $dropdown_html = '';
       
            
                    if (__THIS_PAGE__ == basename($link_array['url'], '.php')) {
                        $is_active = ' active';
                    }

                    $array = [
                        'ACTIVE' => $is_active,
                        'DROPDOWN_URL_TEXT' =>  $link_array['text'],
                        'DROPDOWN_URL' => $link_array['url'],
                    ];

                    $dropdown_link_html .= Render::html('base/navbar/menu_dropdown_link', $array);
                
            
        } else {
            if (true == $link_array['studio']) {
                if ($_REQUEST['studio']) {
                    $url = $link_array['url'].'?studio='.$_REQUEST['studio'];
                }
                if ($_REQUEST['substudio']) {
                    $url = $link_array['url'].'?substudio='.$_REQUEST['substudio'];
                }
            }

            if (__THIS_PAGE__ == basename($link_array['url'], '.php')) {
                $is_active = ' active';
            }
            $array = [
                'MENULINK_URL' => $link_array['url'],
                'MENULINK_JS' => $link_array['js'],
                'MENULINK_TEXT' => $link_array['text'],
                'MENULINK_ICON' => Functions::navbarIcon($link_array),
                'ACTIVE' => $is_active,
            ];

            $url_text = Render::html('base/navbar/menu_link', $array);

            if (true == $link_array['secure'] && 'bjorn' != $_SERVER['REMOTE_USER']) {
                $html = $html.$url_text."\n";
            } else {
                $html = $html.$url_text."\n";
            }
        } // end if
    } // end foreach
    


    $dropdown_html .= Render::html('base/navbar/menu_dropdown',     [
        'DROPDOWN_TEXT' => "Extra",
        'DROPDOWN_LINKS' => $dropdown_link_html,
    ]
);

    return $html.$dropdown_html;
} // end navbar_links()










foreach ($test_link as $name => $row) {
    if ('dropdown' != $name) {
        foreach ($row as $key => $value) {
            if (is_bool($value)) {
                $Checked = '';
                if (1 == $value) {
                    $Checked = ' checked ';
                }
                $checkboxes .= Render::return('Settings/form/Navigation/checkbox',['Name' => $key, 'Checked' => $Checked]);
                continue;
            }
            $cardRows .= Render::return('Settings/form/Navigation/text_row', ['Name' => $key, 'Value' => $value]);
        }

        $cardRows .= Render::return('Settings/form/Navigation/checkbox_row',['CardCheckbox' => $checkboxes]);
        $CardContent .= Render::return('Settings/form/Navigation/card_text',['LinkName' => $name, 'CardFormContent' => $cardRows]);
        $cardRows = '';
        $checkboxes = '';
        // break;
    } else {
        foreach($row as $ddName => $ddUrl){
            dump($ddName,$ddUrl);
        }
    }

}

$card = Render::return('Settings/form/Navigation/card',   ['CardContent' => $CardContent]);
$body = Render::html('Settings/page', ['html'=>$card,'nav'=>navbar_links()]);

Render::Display($body);
