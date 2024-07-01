<?php
/**
 *  Plexweb
 */

namespace Plex\Template;

use Plex\Modules\Display\Layout;
use UTMTemplate\Render as UTMTemplateRender;

class Render extends UTMTemplateRender
{
    // public function __construct() {}

    public static function Display($array = '', $template = 'base/body')
    {
        Layout::Header();
        self::echo($template, ['BODY' => $array]);
        Layout::Footer();
    }


}
