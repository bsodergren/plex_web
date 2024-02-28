<?php

namespace Plex\Modules\Video\Playlist;

use Plex\Modules\Video\Player;
use Plex\Template\Functions\Functions;
use Plex\Template\HTML\Elements;
use Plex\Template\Render;

class Playlist extends Player
{
    
    public function __construct()
    {
        $this->playlistTemplate = parent::$VideoTemplate;
    }

    public function playlistId()
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
        }

        return $this->playlist_id;
    }

    private function addSearchBox()
    {
        $hiddenList = '';
        $cols = ['id', 'search_id', 'name'];
        $this->db->where('id', $this->playlist_id);

        $playlist_info = $this->db->getOne(Db_TABLE_PLAYLIST_DATA, null, $cols);
        $this->playlistName = $playlist_info['name'];
        if (null !== $playlist_info['search_id']) {
            $this->db->where('id', $playlist_info['search_id']);
            $search_result = $this->db->getOne(Db_TABLE_SEARCH_DATA, null, ['video_list']);

            foreach (explode(',', $search_result['video_list']) as $video_id) {
                $hiddenList .= Elements::add_hidden('playlist[]', $video_id);
            }
            $this->canvas_form = Render::html(
                $this->videoTemplate.'/canvas/form',
                [
                    'search_id' => $playlist_info['search_id'],
                    'playlist_list' => $hiddenList,
                    'playlist_name' => $playlist_info['name'],
                ]);
        }
    }

    private function getPlaylistItem($row, $class, $type)
    {
        $title = $row['title'];
        if ('' == $row['title']) {
            $title = $row['filename'];
        }

        return Render::html(
            $this->videoTemplate.'/'.$type.'/item',
            [
                'THUMBNAIL' => ( new Functions())->fileThumbnail($row['playlist_video_id'], 'alt="#" class="img-fluid" '),
                'STUDIO' => $row['studio'],
                'ARTIST' => $row['artist'],
                'GENRE' => $row['genre'],
                'PLAYLIST_ID' => $this->playlist_id,
                'CLASS_ACTIVE' => $class,
                'VIDEO_ID' => $row['playlist_video_id'],
                'VIDEO_URL' => $this->getVideoURL($row['playlist_video_id']),
                'TITLE' => $title,
            ]
        );
    }

    public function getPlaylist()
    {
        $sql = 'select
                    f.thumbnail,f.filename,p.playlist_video_id,
                    m.title,
                    m.genre,
                    m.studio,
                    m.artist
                    from
                    '.Db_TABLE_VIDEO_FILE.' as f,
                    '.Db_TABLE_PLAYLIST_VIDEOS.' as p,
                    '.Db_TABLE_VIDEO_TAGS.' as m where (
                    p.playlist_id = '.$this->playlist_id.' and
                    p.playlist_video_id = f.id  and
                    f.video_key = m.video_key);';
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

            $this->carousel_item .= $this->getPlaylistItem($results[$i], $class, 'carousel');
            $this->canvas_item .= $this->getPlaylistItem($results[$i], $class, 'canvas');
            $this->plyr_item .= $this->getPlaylistItem($results[$i], $class, 'playlist');

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
        $this->params['RemoveVideo'] = $this->getRemoveVideo();
        // $this->params['PLAYLIST_HEIGHT'] = $playlist_height;

        // $this->params['CAROUSEL_HTML' => $this->getCarousel();
        // $this->params['CANVAS_HTML'] = $this->getCanvas();
        // $this->params['CAROUSEL_JS'] = $this->getCarouselScript();
        $this->params['PLYRLISTHTML'] = $this->getPlyrList();
        $this->params['hasPlaylist'] = 'true';
        $this->js_params['PLAYLIST_ID'] = $this->playlist_id;
        $this->js_params['NEXT_VIDEO_ID'] = $next_video_id;
        $this->js_params['PREV_VIDEO_ID'] = $prev_video_id;
        $this->js_params['COMMENT'] = '';
    }
}
