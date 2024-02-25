<?php

namespace Plex\Template;

use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;

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
        return self::return($template, $replacement_array);
    } // end Render::html()

    public static function javascript($template, $replacement_array = '')
    {
        return self::return($template, $replacement_array, 'js');
    } // end Render::html()

    public static function stylesheet($template, $replacement_array = '')
    {
        return self::return($template, $replacement_array, 'css');
    }

    public static function echo($template = '', $array = '')
    {
        $template_obj = new Template();
        $template_obj->template($template, $array);

        //  $template_obj->html = $template_obj->parse_urllink($template_obj->html);

        $indenter = new \Gajus\Dindent\Indenter();
        // $template_obj->html=$indenter->indent($template_obj->html);
        echo $template_obj->html;
    }

    public static function Display($array = '')
    {
        Header::Display();
        self::echo('base/page', ['BODY' => $array]);

        Footer::Display();
    }

    public static function return($template = '', $array = '', $js = '')
    {
        $template_obj = new Template();
        $template_obj->template($template, $array, $js);

        return $template_obj->html;
    }
}
