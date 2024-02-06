<?php
namespace Plex\Template\Functions;

use Plex\Template\Render;
use Plex\Template\Display\Display;
/**
 * plex web viewer
 */

class metaFilters extends Render
{
    public function __call($name, $arguments)
    {
        $filter_html = Display::filter($name);
        foreach ($_REQUEST as $name => $value) {
            if ('' != $value) {
                $hidden .= add_hidden($name, $value);
            }
        }
        return Render::html('elements/metaFilter/block', ['HIDDEN' => $hidden, 'FILTER_HTML' => $filter_html]);

    }
}
