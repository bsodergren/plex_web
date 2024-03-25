<?php

namespace Plex\Modules\Video;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Database\VideoDb;
use Plex\Modules\Playlist\Playlist;
use Plex\Modules\Video\Player\Plyr;
use Plex\Modules\Video\Player\VideoJs;
use Plex\Modules\Video\Playlist\PlyrList;
use Plex\Template\Render;
use UTMTemplate\HTML\Elements;

class Player
{
    // public $playlist_id;
    public $id;
    public $db;
    // public $playlistName;
    // public $templateRoot = 'Video';
    // public $canvas_form;
    // public $carousel_item;
    // public $canvas_item;
    // public $chapterIndex;
    // public $js_params = [];
    // public $params = [
    // ];

    public $VideoTemplate = 'Video/Plyr';
    public static $PlayerTemplate = '';
    public $options = [
        'usePlyr' => true,
        'useCanvas' => false,
        'useCarousel' => false,
        'usePlyrPlaylist' => true,
        'useVideoJs' => false,
    ];

    public function __construct()
    {
        self::$PlayerTemplate = $this->VideoTemplate;
        $this->db = PlexSql::$DB;
        $this->playlistId();
        utmdump('this->playlist  ID '.$this->playlist_id);
    }

    public function PlayVideo()
    {
        if (true == $this->options['usePlyr']) {
            $this->PlayerClass = new Plyr($this);
        } elseif (true == $this->options['useVideoJs']) {
            $this->PlayerClass = new videoJs();
        }
        // / $this->PlayerClass->videoId();

        $this->VideoDetails();
       if (\is_object($this->playlist)) {
            $this->params = array_merge($this->params, $this->playlist->params);
            $this->js_params = array_merge($this->js_params, $this->playlist->js_params);
       }

        $this->getVideo();

    }

    public function options($options = [])
    {
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
    }

    public function getPlayerTemplate($template)
    {
        return $this->PlayerClass->templatePlayer.'/'.$template;
    }

    public function getVideoURL($video_id)
    {
        $cols = ['fullpath', 'filename'];
        $this->db->where('id', $video_id);

        $result = $this->db->getOne(Db_TABLE_VIDEO_FILE, null, $cols);

        $fullpath = str_replace(__PLEX_LIBRARY__, APP_HOME.'/videos', $result['fullpath']);

        return $fullpath.'/'.$result['filename'];
    }

    public function getVideo()
    {
        $this->params['VIDEO_ID'] = $this->id;
        if (null === $this->playlist_id) {
            $this->params['hasPlaylist'] = false;
        } else {
            $this->params['PLAYLIST_ID'] = $this->playlist_id;
        }
        $this->params['__LAYOUT_URL__'] = __LAYOUT_URL__;

        $this->params['VIDEO_JS'] = $this->videoJs();

        // return ['VideoContainer'
    }

    public function VideoDetails()
    {
        $res = (new VideoDb())->getVideoDetails($this->videoId());
        $result = $res[0];
        $active_title = null;
        if (null === $active_title) {
            $active_title = $result['filename'];
        }
        if (null === $result['width']) {
            $result['width'] = 1920;
        }
        if (null === $result['height']) {
            $result['height'] = 1080;
        }

        $video_file = $this->getVideoURL($result['id']);

        $this->params['PAGE_TITLE'] = $result['title'];
        $this->params['thumbnail'] = APP_HOME.$result['thumbnail'];

        $this->params['Video_studio'] = $result['studio'];
        $this->params['Video_substudio'] = $result['substudio'];
        $this->params['Video_Genre'] = $result['genre'];
        $this->params['STAR_RATING'] = $result['rating'];
        $this->params['VIDEO_URL'] = $video_file;
        $this->params['height'] = $result['height'];
        $this->params['VIDEO_TITLE'] = $active_title;
        // $this->params['AddChapter'] = $this->addChapter();
        // $this->params['ChapterButtons'] = $this->Chapters->getChapterButtons();
        // $this->js_params['ChapterIndex'] = $this->getChapterJson();
        $this->js_params['height'] = $result['height'];
        $this->js_params['width'] = $result['width'];
    }

    public function videoId()
    {
        if (\array_key_exists('id', $_REQUEST)) {
            $this->id = $_REQUEST['id'];
        }

        return $this->id;
    }

    public function videoJs()
    {
        // utmdump($this->VideoTemplate);
        return Render::javascript($this->VideoTemplate.'/video_js', $this->js_params);
    }

    public function getRemoveVideo()
    {
        $videoId = Elements::add_hidden('videoId', $this->id);
        $videoId .= Elements::add_hidden('playlistid', $this->playlist_id);

        return Render::html($this->videoTemplate.'/buttons/remove', ['HIDDEN_VIDEO_ID' => $videoId]);
    }

    public function getPlaylist()
    {
        $this->playlist = new PlyrList();
        $this->playlist->playlist_id = $this->playlist_id;
        $this->playlist->getPlaylist();

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
}
