<?php

namespace Plex\Template\Pageinate;

/*
 * plex web viewer
 */

class GenrePagenate extends Pageinate
{
    public $table   = Db_TABLE_GENRE;
    public $library = false;

    public function __construct($currentPage, $urlPattern)
    {
        parent::__construct(false, $currentPage, $urlPattern);
    }
}
