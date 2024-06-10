<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Video;

use Plex\Modules\Database\FavoriteDB;
use Plex\Modules\Database\PlexSql;
use Plex\Modules\Database\VideoDb;
use Plex\Modules\Display\FavoriteDisplay;
use Plex\Modules\Playlist\Playlist;
use Plex\Modules\Video\Player\Plyr;
use Plex\Modules\Video\Playlist\Favorites;
use Plex\Modules\Video\Playlist\PlyrList;
use Plex\Template\Render;

class Player
{
    public $id;
    public $db;
    public object $Chapters;
    public $playlist_id;
    public $params;

    public $js_params;
    public $playlistObj;

    public $favorites;
    public $showFavorites = false;

    public $PlayerClass;

    public $VideoTemplate = 'pages/Video/Plyr';

    public static $PlayerTemplate = 'pages/Video/Plyr';

    public $options = [
        'usePlyr'         => true,
        'useCanvas'       => false,
        'useCarousel'     => false,
        'usePlyrPlaylist' => true,
        'useVideoJs'      => false,
    ];

    private $VideoParams = [
        'title'     => 'PAGE_TITLE',
        'thumbnail' => 'thumbnail',

        'studio'    => 'Video_studio',
        'substudio' => 'Video_substudio',

        'genre'       => 'Video_Genre',
        'artist'      => 'Video_Artist',
        'rating'      => 'STAR_RATING',
        'video_file'  => 'VIDEO_URL',
        'video_title' => 'active_title',
        'id'          => 'Videoid',
        'height'      => 'height',
    ];

    private $VideoJsParams = [
        'width'  => 'width',
        'height' => 'height',
    ];

    public function __construct()
    {
        self::$PlayerTemplate = $this->VideoTemplate;
        $this->db             = PlexSql::$DB;

        if (\array_key_exists('favorites', $_REQUEST)) {
            // if (!is_object($this->favorites))
            // {
            $this->showFavorites = true;
            $this->favoriteList();
        // }
        } else {
            $this->getPlaylist();
        }
    }

    private function parseParams($row)
    {
        foreach ($row as $field => $value) {
            if (\array_key_exists($field, $this->VideoParams)) {
                if ('thumbnail' == $field) {
                    $value = __URL_HOME__.$value;
                }
                $this->params[$this->VideoParams[$field]] = $value;
            }
        }

        // utmdd($this->params);
    }

    public function PlayVideo()
    {
        $this->PlayerClass = new Plyr($this);

        $this->VideoDetails();

        if (\is_object($this->favorites)) {
            $this->params    = array_merge($this->params, $this->favorites->params);
            $this->js_params = array_merge($this->js_params, $this->favorites->js_params);
        } elseif (null !== $this->playlistObj) {
            $this->params    = array_merge($this->params, $this->playlistObj->params);
            $this->js_params = array_merge($this->js_params, $this->playlistObj->js_params);
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
        return self::$PlayerTemplate.\DIRECTORY_SEPARATOR.$template;
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
        $this->params['Videoid'] = $this->id;

        // if (null === $this->playlist_id) {
        //     $this->params['hasPlaylist'] = false;
        // } else {
        //     $this->params['PLAYLIST_ID'] = $this->playlist_id;
        // }
        // if (true === $this->showFavorites) {
        //     $this->params['hasPlaylist'] = 'true';
        // }

        $this->params['__LAYOUT_URL__'] = __LAYOUT_URL__;

        $this->params['VIDEO_JS'] = $this->videoJs();

        // return ['VideoContainer'
    }

    public function VideoDetails()
    {
        $res          = (new VideoDb())->getVideoDetails($this->videoId());
        $result       = $res[0];
        $active_title = null;
        if (null === $active_title) {
            $result['active_title'] = $result['filename'];
        }
        if (null === $result['width']) {
            $result['width'] = 1920;
        }
        if (null === $result['height']) {
            $result['height'] = 1080;
        }

        $result['video_file'] = $this->getVideoURL($result['id']);
        $this->parseParams($result);

        // $this->params['PAGE_TITLE'] = $result['title'];
        // $this->params['thumbnail'] = APP_HOME.$result['thumbnail'];

        if (true === FavoriteDB::get($this->id)) {
            $this->params['FAVORITE'] = FavoriteDisplay::RemoveFavoriteVideo($this->id);
        } else {
            $this->params['FAVORITE'] = FavoriteDisplay::addFavoriteVideo($this->id);
        }

        // $this->params['Video_studio'] = $result['studio'];
        // $this->params['Video_substudio'] = $result['substudio'];
        // $this->params['Video_Genre'] = $result['genre'];
        // $this->params['Video_Artist'] = $result['artist'];
        // $this->params['STAR_RATING'] = $result['rating'];
        // $this->params['VIDEO_URL'] = $video_file;
        // $this->params['height'] = $result['height'];
        // $this->params['VIDEO_TITLE'] = $active_title;
        $this->params['Videoid']   = $result['id'];
        $this->js_params['height'] = $result['height'];
        $this->js_params['width']  = $result['width'];
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
        return Render::javascript($this->VideoTemplate.'/video_js', $this->js_params);
    }

    public function favoriteList()
    {
        $this->favorites = new Favorites();
        $this->favorites->getFavoriteList();
        $this->id = $this->favorites->id;
    }

    public function getPlaylist()
    {
        $this->playlistObj = new PlyrList();
        $this->playlist_id = $this->playlistObj->getplaylistId();
        if (null === $this->playlist_id) {
            $this->playlistObj = null;
        } else {
            $this->id                       = $this->playlistObj->id;
            $this->playlistObj->playlist_id = $this->playlist_id;
            $this->playlistObj->getPlaylist();
        }
    }
}
