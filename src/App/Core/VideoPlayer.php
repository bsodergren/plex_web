<?php

namespace Plex\Core;

use Plex\Template\Functions\Functions;
use Plex\Template\HTML\Elements;
use Plex\Template\Render;

class VideoPlayer
{
    public $playlist_id = null;
    public $id;
    public $db;
    public $playlistName;
    public $videoTemplate = 'videoPlyr';
    public $canvas_form;
    public $carousel_item;
    public $canvas_item;
    public $chapterIndex = null;
    public $js_params = [];
    public $params = [
    ];

    public function __construct()
    {
        global $db;
        $this->db = $db;
        $this->js_params['COMMENT'] = '//';
        $this->videoId();
        $this->playlistId();
    }

    public function getVideo()
    {
        $this->params['VIDEO_ID'] = $this->id;
        $this->params['PLAYLIST_ID'] = $this->playlist_id;
        $this->params['__LAYOUT_URL__'] = __LAYOUT_URL__;
 
        $this->params['VIDEO_JS'] = $this->videoJs();
    }

    public function videoInfo()
    {

        $res = (new FileListing(new Request))->getVideoDetails($this->id);
        $result = $res[0];
        $active_title = null; 

        if (null === $active_title) {
            $active_title = $result['filename'];
        }

        $fullpath = str_replace(__PLEX_LIBRARY__, APP_HOME.'/videos', $result['fullpath']);
        $video_file = $fullpath.'/'.$result['filename'];

        $this->params['PAGE_TITLE'] = $result['title'];
        $this->params['thumbnail'] = APP_HOME . $result['thumbnail'];
        
        $this->params['Video_studio'] = $result['studio'];
        $this->params['Video_substudio'] = $result['substudio'];
        $this->params['Video_Genre'] = $result['genre'];
        $this->params['STAR_RATING'] = $result['rating'];
        $this->params['VIDEO_URL'] = $video_file;
        $this->params['height'] = $result['height'];
        $this->params['VIDEO_TITLE'] = $active_title;
        $this->params['AddChapter'] = $this->addChapter();
        $this->params['ChapterButtons'] = $this->getChapterButtons();
        $this->js_params['ChapterIndex'] = $this->getChapterJson();
        $this->js_params['height'] =  $result['height'];
        $this->js_params['width'] =  $result['width'];
    }

    public function videoId()
    {
        if (\array_key_exists('id', $_REQUEST)) {
            $this->id = $_REQUEST['id'];
        }

        return $this->id;
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
                $this->videoTemplate . '/canvas/form',
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
            $this->videoTemplate . '/'.$type.'/item',
            [
                'THUMBNAIL' => ( new Functions())->fileThumbnail($row['playlist_video_id'], 'alt="#" class="img-fluid" '),
                'STUDIO' => $row['studio'],
                'ARTIST' => $row['artist'],
                'GENRE' => $row['genre'],
                'PLAYLIST_ID' => $this->playlist_id,
                'CLASS_ACTIVE' => $class,
                'VIDEO_ID' => $row['playlist_video_id'],
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
        $this->params['CANVAS_HTML'] = $this->getCanvas();
        $this->params['CAROUSEL_JS'] = $this->getCarouselScript();
        
        $this->js_params['PLAYLIST_ID'] = $this->playlist_id;
        $this->js_params['NEXT_VIDEO_ID'] = $next_video_id;
        $this->js_params['PREV_VIDEO_ID'] = $prev_video_id;
        $this->js_params['COMMENT'] = '';
    }

    public function getCarousel()
    {
        return Render::html($this->videoTemplate . '/carousel/block', ['CAROUSEL_INNER_HTML' => $this->carousel_item]);
    }

    public function getCarouselScript()
    {
        return Render::html($this->videoTemplate .'/carousel/js', ['PLAYLIST_ID' => $this->playlist_id]);
    }

    public function getCanvas()
    {
        $this->addSearchBox();

        return Render::html($this->videoTemplate .'/canvas/block', ['CANVAS_LIST' => $this->canvas_item,
            'PlaylistName' => $this->playlistName, 'Canvas_Form' => $this->canvas_form]);
    }

    public function videoJs()
    {
        return Render::javascript($this->videoTemplate .'/video_js', $this->js_params);
    }

    public function getRemoveVideo()
    {
        $videoId = Elements::add_hidden('videoId', $this->id);
        $videoId .= Elements::add_hidden('playlistid', $this->playlist_id);
        return Render::html($this->videoTemplate .'/buttons/remove', ['HIDDEN_VIDEO_ID'=> $videoId]);
    }
    public function addChapter()
    {
        $videoId = Elements::add_hidden('videoId', $this->id);
        if($this->playlist_id != null){
            $videoId .= Elements::add_hidden('playlistid', $this->playlist_id);
        }

        return Render::html($this->videoTemplate .'/buttons/addChapter', ['HIDDEN_VIDEO_ID'=> $videoId]);
    }
    
    public function getChapterButtons()
    {
        $index = $this->getChapters();
        foreach($index as $i => $row){
            $editableClass = 'edit'.$row['time'];
            $functionName = 'make'.$row['time'].'Editable';

            $row['EDITABLE'] = $editableClass;

            $row['VIDEOINFO_EDIT_JS'] = Render::javascript(
                $this->videoTemplate.'/buttons/chapter',
                [
                    'ID_NAME' => $row['time'],
                    'EDITABLE' => $editableClass,
                    'FUNCTION' => $functionName,
                    'VIDEO_KEY' =>$this->id,
                ]
            );
            $html .= Render::html($this->videoTemplate .'/buttons/chapter', $row);
        }
        return $html;
       // 

    }
    public function getChapterJson()
    {
        return json_encode($this->getChapters());
    }
    public function getChapters()
    {
if($this->chapterIndex == null){
        $this->db->where('video_id', $this->id);
        $this->db->orderBy('timeCode', 'ASC');
        $search_result = $this->db->get(Db_TABLE_VIDEO_CHAPTER);
        foreach($search_result as $i => $row)
        {
            if($row['name'] === null){
                $row['name'] = "Timestamp";
            }

            $this->chapterIndex[] = ['time'=>$row['timeCode'],'label'=>$row['name']];

        }
    }
        dump($this->chapterIndex);
        return $this->chapterIndex;

    }
    
}