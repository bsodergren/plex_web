<?php

namespace Plex\Modules\Video\Playlist;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Playlist\Playlist;
use Plex\Modules\Video\Player;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;

class PlyrList extends Player
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

    public function getplaylistId()
    {
        if (\array_key_exists('playlist_id', $_REQUEST)) {
            $playlist_id = $_REQUEST['playlist_id'];
            if (!\array_key_exists('id', $_REQUEST)) {
                $cols = ['playlist_id', 'playlist_video_id'];
                $this->db->where('playlist_id', $playlist_id);

                $playlist_result = $this->db->getOne(Db_TABLE_PLAYLIST_VIDEOS, null, $cols);
                $query = $this->db->getLastQuery();
                $id = $playlist_result['playlist_video_id'];
                $this->id = $id;
            }
            $this->playlist_id = $playlist_id;
        } else {
            $pl = (new Playlist())->getVideoPlaylists($this->videoId());
            foreach ($pl as $k => $row) {
                if (\array_key_exists('playlist_id', $row)) {
                    $this->playlist_id = $row['playlist_id'];
                }
            }
        }

        return $this->playlist_id;
    }

    private function getPlaylistItem($row, $class = '')
    {

        $title = $row['title'];
        if ('' == $row['title']) {
            $title = $row['filename'];
        }
        $thumbImg =  ( new Functions())->fileThumbnail($row['playlist_video_id'], 'alt="#" class="img-fluid" ');
        // utminfo($thumbImg);
        return Render::html(
            $this->videoTemplate.'/container/item',
            [
                'THUMBNAIL' =>$thumbImg,
                'STUDIO' => $row['studio'],
                'ARTIST' => $row['artist'],
                'GENRE' => $row['genre'],
                'width' => $row['width'],
                'height' => $row['height'],
                // 'Rating' => $row['rating'],

                'PLAYLIST_ID' => $this->playlist_id,
                'CLASS_ACTIVE' => $class,
                'Videoid' => $row['playlist_video_id'],
                'VIDEO_URL' => $this->getVideoURL($row['playlist_video_id']),
                'TITLE' => $title,
            ]
        );
    }

    public function getPlaylist()
    {
        $next_video_id = null;
        $prev_video_id = null;

        $sql = 'select
                    v.thumbnail,v.filename,p.playlist_video_id,
                    m.title,
                    m.genre,
                    m.studio,
                    m.artist,
                    i.width,
                    i.height
                    from
                    '.Db_TABLE_VIDEO_FILE.' as v,
                    '.Db_TABLE_VIDEO_INFO.' as i,
                    '.Db_TABLE_PLAYLIST_VIDEOS.' as p,
                    '.Db_TABLE_VIDEO_METADATA.' as m where (
                    p.playlist_id = '.$this->playlist_id.' and
                    p.playlist_video_id = v.id  and
                    v.video_key = m.video_key
                    and
                    v.video_key = i.video_key);';

        $results = $this->db->query($sql);

        $newArray = [];
        $test = $results;
        foreach ($test as $index => $row) {
            if ($row['playlist_video_id'] == $this->id) {
                break;
            }
            $last = array_shift($test);
            $newArray[] = $last;
        }

        $results = array_merge($test, $newArray);

        for ($i = 0; $i < \count($results); ++$i) {
            $class = '';
            if ($this->id == $results[$i]['playlist_video_id']) {
                $class = ' active';
            }

            $this->plyr_item .= $this->getPlaylistItem($results[$i], $class);

            if (' active' == $class) {
                $indx = $i + 1;
                if (\array_key_exists($indx, $results)) {
                    $next_video_id = $results[$indx]['playlist_video_id'];
                } else {
                    $next_video_id = $results[0]['playlist_video_id'];
                }

                $pndx = $i - 1;
                if (\array_key_exists($pndx, $results)) {
                    $prev_video_id = $results[$pndx]['playlist_video_id'];
                } else {
                    $prev_video_id = $results[0]['playlist_video_id'];
                }
            }
        }

        $this->params['PLYRLISTHTML'] = $this->plyr_item;
        $this->params['hasPlaylist'] = 'true';
        $this->js_params['PLAYLIST_ID'] = $this->playlist_id;
        $this->js_params['NEXT_VIDEO_ID'] = $next_video_id;
        $this->js_params['PREV_VIDEO_ID'] = $prev_video_id;
        $this->js_params['COMMENT'] = '';
    }
}
