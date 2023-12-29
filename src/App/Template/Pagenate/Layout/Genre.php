<?php
/**
 * plex web viewer
 */

namespace Plex\Template\Pagenate\Layout;

use Plex\Template\Pagenate\Pageinate;

class Genre extends Pageinate
{
    public $table   = Db_TABLE_GENRE;
    public $library = false;

    public function __construct($currentPage, $urlPattern)
    {
        parent::__construct(false, $currentPage, $urlPattern);
    }
}
