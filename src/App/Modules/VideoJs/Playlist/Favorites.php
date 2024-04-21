<?php

namespace Plex\Modules\Video\Playlist;

use Plex\Modules\Database\FavoriteDB;
use Plex\Modules\Database\PlexSql;
use Plex\Modules\Video\Player;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;

class Favorites extends Player
{
    public $videoTemplate;
    public $db;
    public $playlist;
    public $playlist_id;
    public $playlistName;
    public $video_id;
    public $plyr_item;

    public function __construct()
    {
        $this->videoTemplate = parent::$PlayerTemplate;
        $this->db = PlexSql::$DB;
    }

    private function getFavoriteItem($row, $class)
    {
        $title = $row['title'];
        if ('' == $row['title']) {
            $title = $row['filename'];
        }
        utmdump($row);

        return Render::html(
            $this->videoTemplate.'/container/item',
            [
                'THUMBNAIL' => ( new Functions())->fileThumbnail($row['id'], 'alt="#" class="img-fluid" '),
                'STUDIO' => $row['studio'],
                'ARTIST' => $row['artist'],
                'GENRE' => $row['genre'],
                'Rating' => $row['rating'],
                'CLASS_ACTIVE' => $class,
                'Videoid' => $row['id'],
                'VIDEO_URL' => $this->getVideoURL($row['id']),
                'TITLE' => $title,
            ]
        );
    }

    public function getFavoriteList()
    {
        $results = (new FavoriteDB())->getFavoriteVideos();

        if (\count($results) > 0) {
            $this->id = $results[0]['id'];
            for ($i = 0; $i < \count($results); ++$i) {
                $class = '';
                if ($this->id == $results[$i]['id']) {
                    $class = ' active';
                }
                $this->plyr_item .= $this->getFavoriteItem($results[$i], $class);

                $this->params['PLYRLISTHTML'] = $this->plyr_item;
                $this->params['hasPlaylist'] = 'true';
                // $this->js_params['PLAYLIST_ID'] = 1;
                // $this->js_params['NEXT_VIDEO_ID'] = $next_video_id;
                // $this->js_params['PREV_VIDEO_ID'] = $prev_video_id;
                $this->js_params['COMMENT'] = '';
            }
        }
    }
}
