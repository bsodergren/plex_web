<?php

namespace Plex\Template;

use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
use UTMTemplate\Render as UTMTemplateRender;

class Render extends UTMTemplateRender
{
    public function __construct() {}

    public static function Display($array = '')
    {
        Header::Display();
        self::echo('base/page', ['BODY' => $array]);
        Footer::Display();
    }

}
