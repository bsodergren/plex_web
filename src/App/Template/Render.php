<?php

namespace Plex\Template;

use Plex\Modules\Display\Layout;
use UTMTemplate\Render as UTMTemplateRender;

class Render extends UTMTemplateRender
{
    public function __construct() {}

    public static function Display($array = '')
    {
        Layout::Header();
        self::echo('base/page', ['BODY' => $array]);
        Layout::Footer();
    }
}
