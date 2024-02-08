<?php

namespace Plex\Template\Functions;

use Plex\Core\RoboLoader;
use Plex\Template\Render;
use Plex\Core\FileListing;
use Plex\Template\HTML\Elements;

use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
use Plex\Template\Display\Display;
use Plex\Template\Functions\Traits\Video;
use Plex\Template\Functions\Traits\Navbar;
use Plex\Template\Functions\Modules\AlphaSort;
use Plex\Template\Functions\Traits\Breadcrumbs;
use Plex\Template\Functions\Modules\metaFilters;
use Plex\Template\Functions\Traits\PageSort;
use Plex\Template\Functions\Traits\ThemeSwitcher;

class Functions extends Render
{
    use Breadcrumbs;
    use Navbar;
    use Video;
use ThemeSwitcher;
use PageSort;

public function __construct() {}

    public function hiddenSearch()
    {
        if (null === FileListing::$searchId) {
            return '';
        }

        return Elements::add_hidden('search_id', FileListing::$searchId);
    }

    private function parseVars($matches)
    {
        $parts = explode(',', $matches[2]);
        foreach ($parts as $value) {
            if (str_contains($value, '=')) {
                $v_parts = explode('=', $value);
                if (str_contains($v_parts[0], '?')) {
                    $q_parts = explode('?', $v_parts[0]);

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

    public function displayFilters()
    {
        
        return (new metaFilters())->displayFilters();
    }
    public function metaFilters($match)
    {
        $method = $match[2];

        return (new metaFilters())->{$method}();
    }

    public function playListButton()
    {
        $params['CANVAS_HEADER'] = Render::html('elements/Playlist/canvas_header', []);
        $params['CANVAS_BODY'] = Render::html('elements/Playlist/canvas_body', []);

        return Render::html('elements/Playlist/canvas', $params);
        // dump($html);
    }

    public function AlphaBlock($match)
    {
        return (new AlphaSort())->displayAlphaBlock();
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

}
