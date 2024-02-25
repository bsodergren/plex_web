<?php

namespace Plex\Template\Functions;

use Plex\Core\RoboLoader;
use Plex\Template\Render;
use Plex\Modules\Database\FileListing;
use Plex\Template\HTML\Elements;
use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
use Plex\Template\Functions\Traits\Icons;
use Plex\Template\Functions\Traits\Video;
use Plex\Template\Functions\Traits\Navbar;
use Plex\Template\Functions\Traits\PageSort;
use Plex\Template\Functions\Modules\AlphaSort;
use Plex\Template\Functions\Traits\Breadcrumbs;
use Plex\Template\Functions\Modules\metaFilters;
use Plex\Template\Functions\Traits\ThemeSwitcher;

class Functions extends Render
{
    use Breadcrumbs;
    use Icons;
    use Navbar;
    use PageSort;
    use ThemeSwitcher;
    use Video;

    public function __construct()
    {

        $dir = __HTML_TEMPLATE__."/".$this->IconsDir;
        $include_array =  RoboLoader::get_filelist($dir, 'html', 1);
        foreach($include_array as $required_file) {
            $this->iconList[] = basename($required_file,".html");
        }



    }

    public function __call($name, $arguments)
    {
        if (\in_array($name, $this->iconList)) {
            //dump([__METHOD__,$arguments[0]]);
            return $this->getIcon($name, $arguments[0]);
        }
        return false;
    }

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
