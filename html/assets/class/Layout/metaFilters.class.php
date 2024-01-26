<?php
/**
 * plex web viewer
 */

include_once __PHP_INC_CLASS_DIR__.'/Render.class.php';
include_once __PHP_INC_CLASS_DIR__.'/Template.class.php';

class metaFilters extends Render
{
    public function __call($name, $arguments)
    {
        $filter_html = Render::display_filter($name);
        foreach ($_REQUEST as $name => $value) {
            if ('' != $value) {
                $hidden .= add_hidden($name, $value);
            }
        }
        return process_template('elements/metaFilter/block', ['HIDDEN' => $hidden, 'FILTER_HTML' => $filter_html]);

    }
}
