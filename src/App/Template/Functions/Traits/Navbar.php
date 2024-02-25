<?php

namespace Plex\Template\Functions\Traits;

use Plex\Template\Functions\Functions;
use Plex\Template\Render;

trait Navbar
{
    private $NavbarDir = 'base/navbar';

    public static function navbar_links()
    {
        $html = '';
        $dropdown_html = '';
        global $navigation_link_array,$login_link_array;
        global $_REQUEST;

        if (!isset($_SESSION['auth'])
        || 'verified' != $_SESSION['auth']) {
            $navigation_link_array = $login_link_array;
        }

        foreach ($navigation_link_array as $name => $link_array) {
            $is_active = '';
            if ('dropdown' == $name) {
                $dropdown_html = '';

                foreach ($link_array as $dropdown_name => $dropdown_array) {
                    $dropdown_link_html = '';
                    foreach ($dropdown_array as $d_name => $d_values) {
                        $is_active = '';

                        if (str_contains($d_name, 'Divider')) {
                            $dropdown_link_html .= ' <li><hr class="dropdown-divider"></li>';
                            continue;
                        }
                        if (__THIS_PAGE__ == basename($d_values, '.php')) {
                            $is_active = ' active';
                        }

                        $array = [
                            'ACTIVE' => $is_active,
                            'DROPDOWN_URL_TEXT' => $d_name,
                            'DROPDOWN_URL' => $d_values,
                        ];

                        $dropdown_link_html .= Render::html('base/navbar/menu_dropdown_link', $array);
                    }

                    $array = [
                        'DROPDOWN_TEXT' => $dropdown_name,
                        'DROPDOWN_LINKS' => $dropdown_link_html,
                    ];

                    $dropdown_html .= Render::html('base/navbar/menu_dropdown', $array);
                }
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
                    'MENULINK_ICON' => self::navbarIcon($link_array),
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

        return $html.$dropdown_html;
    } // end navbar_links()

    public static function navbarIcon($link_array)
    {
        $html = '';
        if (isset($link_array['icon'])) {
            $icon = $link_array['icon'];
            $html = ' '.(new Functions())->{$icon}();
        }

        return $html;
    }
}
