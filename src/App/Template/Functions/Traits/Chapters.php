<?php

namespace Plex\Template\Functions\Traits;

use Plex\Modules\Database\FavoriteDB;
use Plex\Modules\Database\PlexSql;
use Plex\Modules\Display\FavoriteDisplay;
use Plex\Modules\Playlist\Playlist;
use Plex\Modules\VideoCard\VideoCard;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;
use UTMTemplate\HTML\Elements;
use Plex\Modules\Chapter\Chapter;

trait Chapters
{
    public object $Chapters;
    public function showChapters($matches)
    {
        $var = $this->parseVars($matches);
        utmdump($var);
        $this->Chapters = new Chapter($var);
        $Chapters = $this->Chapters->displayChapters();
        return $Chapters;
    }
}
