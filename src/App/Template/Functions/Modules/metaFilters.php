<?php

namespace Plex\Template\Functions\Modules;

use Plex\Modules\Database\PlexSql;
use Plex\Template\Render;
use UTM\Utilities\Option;
use UTMTemplate\HTML\Elements;

/**
 * plex web viewer.
 */
class metaFilters extends Render
{

    private $templateDir = 'elements/metaFilter';

    public function __call($name, $arguments)
    {
        $hidden = '';
        $filter_html = $this->filter($name);
        foreach ($_REQUEST as $name => $value) {
            if ('' != $value) {
                $hidden .= Elements::add_hidden($name, $value);
            }
        }

        return Render::html($this->templateDir.'/filter', ['HIDDEN' => $hidden, 'FILTER_HTML' => $filter_html]);
    }

    public function displayFilters()
    {

            if (OptionIsTrue(USE_FILTER)) {
                return Render::html($this->templateDir.'/block', []);

        }
    }

    public  function filter($tag)
    {
        $selected          = '';
        $clear             = $tag;
        foreach ($_REQUEST as $name => $value) {
            if ($name == $tag) {
                if ('' != $value) {
                    $selected = $value;
                    $clear    = 'Clear '.$tag;

                    continue;
                }
            }
        }

        $genreArray        = PlexSql::getFilterList($tag);
        $params['NAME']    = $tag;
        $params['OPTIONS'] = Elements::SelectOptions($genreArray, $selected, $clear);

        return Render::html($this->templateDir.'/select_box', $params);
    }


}
