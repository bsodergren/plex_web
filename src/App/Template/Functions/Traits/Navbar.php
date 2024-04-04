<?php

namespace Plex\Template\Functions\Traits;

use Plex\Template\Render;
use UTMTemplate\Template;
use Symfony\Component\Yaml\Yaml;
use Plex\Template\Functions\Functions;

trait Navbar
{
    private $NavbarDir = 'base/navbar';

    public static function dropdownLink($url, $name, $active, $badge)
    {
        return Render::html('base/navbar/menu_dropdown_link', [
            'ACTIVE' => $active,
            'DROPDOWN_URL_TEXT' => $name,
            'DROPDOWN_URL' => $url,
            'DROPDOWN_BADGE' => $badge,
        ]);
    }

    public static function dropdown($array)
    {
        $dropdown_link_html = [];
        $pop = false;
        foreach ($array['dropdown'] as $d_name => $d_values) {
            $is_active = '';

            if (\is_array($d_values)) {
                foreach ($d_values as $dd_name => $dd_url) {
                    $is_active = '';
                    if (__THIS_PAGE__ == basename($dd_url, '.php')) {
                        $is_active = ' active';
                    }
                    $dropdown_link_html[] = self::dropdownLink($dd_url, $dd_name, $is_active, $badge);
                }
                $dropdown_link_html[] = '<li><hr class="dropdown-divider-settings"></li>';
                $pop = true;
                continue;
            }

            if (true === $pop) {
                array_pop($dropdown_link_html);
                $pop = false;
            }

            if (__THIS_PAGE__ == basename($d_values, '.php')) {
                $is_active = ' active';
            }

            $badge = '';
            $parts = explode('|', $d_values);
            $url = $parts[0];

            if (\array_key_exists(1, $parts)) {
                $badge = Render::html('base/navbar/menu_dropdown_badge', ['COUNT' => $parts[1]]);
            }

            $dropdown_link_html[] = self::dropdownLink($url, $d_name, $is_active, $badge);
        }

        if (true === $pop) {
            array_pop($dropdown_link_html);
            $pop = false;
        }

        return Render::html('base/navbar/menu_dropdown', [
            'DROPDOWN_TEXT' => $array['text'],
            'DROPDOWN_LINKS' => implode("\n", $dropdown_link_html),
        ]);
    }

    public static function navbar_links()
    {
        $html = '';
        global $_REQUEST;

        $navigation_link_array = Yaml::parseFile(__ROUTE_NAV__, Yaml::PARSE_CONSTANT);
        if (\defined('PLAYLIST_DROPDOWN')) {
            $navigation_link_array['playlist']['dropdown'] = PLAYLIST_DROPDOWN;
        }

        foreach ($navigation_link_array as $name => $link_array) {
            $is_active = '';
            if (\array_key_exists('dropdown', $link_array)) {
                $html .= self::dropdown($link_array);
                continue;
            }
            if (true == $link_array['studio']) {
                if ($_REQUEST['studio']) {
                    $link_array['url'] = $link_array['url'].'?studio='.$_REQUEST['studio'];
                }
                if ($_REQUEST['substudio']) {
                    $link_array['url'] = $link_array['url'].'?substudio='.$_REQUEST['substudio'];
                }
            }

            if (__THIS_PAGE__ == basename($link_array['url'], '.php')) {
                $is_active = ' active';
            }

            if (true == $link_array['days']) {
                if (__THIS_PAGE__ == 'recent') {
                    $is_active = $is_active . " recent-days-link";
                }
            }

            $array = [
                'MENULINK_URL' => $link_array['url'],
                'MENULINK_JS' => $link_array['js'],
                'MENULINK_TEXT' => $link_array['text'],
                'MENULINK_ICON' => self::navbarIcon($link_array),
                'ACTIVE' => $is_active,
            ];

            $url_text = Render::html('base/navbar/menu_link', $array);

                if (true == $link_array['days']) {
                    if (__THIS_PAGE__ == 'recent') {

                    //$url_text = str_replace("</li>",'',$url_text);
                    $url_text .= (new Functions)->displayDayList();
                    }
                   // $url_text .= '</li>';
                }


            $html = $html.$url_text."\n";
        } // end foreach

        return $html;
    } // end navbar_links()

    public static function navbarIcon($link_array)
    {
        $html = '';
        if (isset($link_array['icon'])) {
            $icon = $link_array['icon'];
            $html = ' '.Template::Icons($icon);
        }

        return $html;
    }
}
