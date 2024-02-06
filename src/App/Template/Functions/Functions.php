<?php
namespace Plex\Template\Functions;

use Plex\Core\RoboLoader;
use Plex\Template\Render;
use Plex\Core\FileListing;
use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
use Plex\Template\Display\Display;
use Plex\Template\Functions\AlphaSort;
use Plex\Template\Functions\metaFilters;

class Functions extends Render
{
    public function __construct() {}

    public function hiddenSearch()
    {
        if(FileListing::$searchId === null){
            return '';
        }

        return add_hidden("search_id", FileListing::$searchId );
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
        $params['CANVAS_BODY'] = Render::html('elements/Playlist/canvas_body', []);
        
        $html= Render::html('elements/Playlist/canvas', $params);
        // dump($html);
        return $html;
        
    }
    public function breadcrumbs($match)
    {
        return Display::breadcrumbs();
    }

    public function videoRating($matches)
    {
        $var = $this->parseVars($matches);

        return Render::html('elements/Rating/rating', ['ROW_ID' => $var['id'], 'STAR_RATING' => $var['rating']]);
    }

    public function ratingInclude($matches)
    {
        return Render::html('elements/Rating/header', []);
    }

    public function AlphaBlock($match)
    {
        return AlphaSort::display_AlphaBlock();
    }

    public function metaFilters($match)
    {
        if(defined("USE_FILTER") == true) {
            if(USE_FILTER == true) {
            $method = $match[2];
            return (new metaFilters)->$method();
        }
    }
        
    }

    public function videoPlayer($matches)
    {
        $var    = $this->parseVars($matches);

        if (is_array($var['query'])) {
            $req = '?'.http_build_query($var['query']);
        }

        $window = basename($var['href'], '.php').'_popup';
        $url    = __URL_HOME__.'/'.$var['href'].$req;

        return " onclick=\"popup('".$url."', '".$window."')\"";
    }

    public function videoButton($matches)
    {
        $var = $this->parseVars($matches);
        if (array_key_exists('pl_id', $var)) {
            if ('' == $var['pl_id']) {
                return '';
            }
        }

        return Render::html('video/buttons/'.$var['template'], []);
    }
    public function pageHeader($matches){
        Header::Display();
    }
    public function pageFooter($matches){
        Footer::Display();
    }

    public function themeSwitcher($matches)
    {

        $css_dir                   = __LAYOUT_PATH__.'/css/themes/';
        $files                     = RoboLoader::get_filelist($css_dir, 'bootstrap.min.css', 0);

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
