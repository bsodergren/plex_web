<?php
/**
 * plex web viewer
 */

namespace Plex\Template\Pagenate\Layout;

use Plex\Template\Pagenate\Pageinate;

class Config extends Pageinate
{
    public $table   = Db_TABLE_STUDIO;
    public $library = false;
}
