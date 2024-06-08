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

    public function showChapters($matches)
    {
        $var = $this->parseVars($matches);
        $this->Chapters = new Chapter($var);
        $Chapters = $this->Chapters->displayChapters();
        utmdump($Chapters);
        return $Chapters;
        // $this->params['ChapterButtons'] =         $Chapters;
    }
}
