<?php

namespace Plex\Modules\Video;

use Plex\Core\Request;
use Plex\Template\Render;
use Plex\Modules\Video\Chapter;
use Plex\Template\HTML\Elements;
use Plex\Modules\Video\Player\Plyr;
use Plex\Modules\Database\FileListing;
use Plex\Modules\Video\Player\VideoJs;

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

    public $options = [
        'usePlyr' => true,
        'useCanvas' => false,
        'useCarousel' => false,
        'usePlyrPlaylist' => true,
        'useVideoJs' => false,
    ]; 

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    public function PlayVideo()
    {
              
        if($this->options['usePlyr'] == true){
            $PlayerClass = new Plyr();
        } elseif($this->options['useVideoJs'] == true) {
            $PlayerClass = new videoJs();
        }
       /// $this->PlayerClass->videoId();
        utmdump($this->id);
            $PlayerClass->ShowVideoPlayer();
    }

    public function options($options = [])
    {
        foreach ($options as $key => $value)
        {
            $this->options[$key] = $value;
        }

    }

public function getPlayerTemplate()
{

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
        $this->params['PLAYLIST_ID'] = $this->playlist_id;
        $this->params['__LAYOUT_URL__'] = __LAYOUT_URL__;

        $this->params['VIDEO_JS'] = $this->videoJs();

        // return ['VideoContainer'
    }

    public function VideoDetails()
    {
        $res = (new FileListing(new Request()))->getVideoDetails($this->id);
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
        utmdump($this->id);
        return $this->id;
    }

    public function videoJs()
    {
        return Render::javascript($this->videoTemplate.'/video_js', $this->js_params);
    }

    public function getRemoveVideo()
    {
        $videoId = Elements::add_hidden('videoId', $this->id);
        $videoId .= Elements::add_hidden('playlistid', $this->playlist_id);

        return Render::html($this->videoTemplate.'/buttons/remove', ['HIDDEN_VIDEO_ID' => $videoId]);
    }
}
