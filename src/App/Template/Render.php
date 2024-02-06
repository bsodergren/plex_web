<?php
/**
 * plex web viewer
 */

namespace Plex\Template;

/**
 * plex web viewer.
 */

/**
 * plex web viewer.
 */
class Render extends Template
{
    public function __construct() {}

    public static function html($template, $replacement_array = '')
    {
        return Template::return($template, $replacement_array);
    } // end Render::html()

    public static function javascript($template, $replacement_array = '')
    {
        return Template::return($template, $replacement_array, 'javascript');
    } // end Render::html()
}
