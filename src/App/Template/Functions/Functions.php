<?php
/**
 * plex web viewer
 */

namespace Plex\Template\Functions;

use Plex\Core\FileListing;
use Plex\Core\RoboLoader;
use Plex\Template\Display\Display;
use Plex\Template\Functions\Traits\Navbar;
use Plex\Template\Functions\Traits\VideoDisplay;
use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
use Plex\Template\Render;

class Functions extends Render
{
    use VideoDisplay;
    use Navbar;

    public function __construct() {}

    public function hiddenSearch()
    {
        if (null === FileListing::$searchId) {
            return '';
        }

        return add_hidden('search_id', FileListing::$searchId);
    }

    private function parseVars($matches)
    {
        $parts = explode(',', $matches[2]);
        foreach ($parts as $value) {
            if (str_contains($value, '=')) {
                $v_parts             = explode('=', $value);
                if (str_contains($v_parts[0], '?')) {
                    $q_parts                      = explode('?', $v_parts[0]);

                    $values['query'][$q_parts[1]] = $v_parts[1];
                    continue;
                }
                $values[$v_parts[0]] = $v_parts[1];
                continue;
            }
            $values['var'][] = $value;
        }

        return $values;
    }

    public function playListButton()
    {
        $params['CANVAS_HEADER'] = Render::html('elements/Playlist/canvas_header', []);
        $params['CANVAS_BODY']   = Render::html('elements/Playlist/canvas_body', []);

        return Render::html('elements/Playlist/canvas', $params);
        // dump($html);
    }


    public function AlphaBlock($match)
    {
        return AlphaSort::display_AlphaBlock();
    }

    public function metaFilters($match)
    {
        if (true == \defined('USE_FILTER')) {
            if (USE_FILTER == true) {
                $method = $match[2];

                return (new metaFilters())->{$method}();
            }
        }
    }

    public function videoButton($matches)
    {
        $var = $this->parseVars($matches);
        if (\array_key_exists('pl_id', $var)) {
            if ('' == $var['pl_id']) {
                return '';
            }
        }

        return Render::html('video/buttons/'.$var['template'], []);
    
    }
    public function pageHeader($matches)
    {
        Header::Display();
    }

    public function pageFooter($matches)
    {
        Footer::Display();
    }
    public  function theme_dropdown()
    {
        $theme_options = Render::html('base/navbar/theme/option', ['THEME_NAME' => 'Default', 'THEME_OPTION' => 'none']);
        foreach (Display::$CSS_THEMES as $theme) {
            $theme_options .= Render::html('base/navbar/theme/option', ['THEME_NAME' => ucfirst($theme).' Theme', 'THEME_OPTION' => $theme.'-theme']);
        }

        return Render::html('base/navbar/theme/select', ['THEME_OPTIONS' => $theme_options]);
    }
    public function themeSwitcher($matches)
    {
        $css_dir = __LAYOUT_PATH__.'/css/themes/';
        $files   = RoboLoader::get_filelist($css_dir, 'bootstrap.min.css', 0);

        foreach ($files as $stylesheet) {
            $dirArray              = explode('/', $stylesheet);
            array_pop($dirArray);
            $theme                 = end($dirArray);
            Display::$CSS_THEMES[] = $theme;
            $stylesheet            = str_replace(__LAYOUT_PATH__, __LAYOUT_URL__, $stylesheet);

            // $name =
            $css_html .= Render::html('base/header/header_css_link', ['CSS_NAME' => $theme, 'CSS_URL' => $stylesheet]);
        }

        return $css_html;
        // }
    }
}
