<?php

namespace Plex\Core;

use Plex\Modules\Database\FileListing;
use Plex\Modules\Database\VideoDb;
use Plex\Template\Functions\Functions;
use Plex\Template\HTML\Elements;
use Plex\Template\Render;

class VideoPlayer
{
    public $playlist_id;
    public $id;
    public $db;
    public $playlistName;
    public $videoTemplate = 'videoPlyr';
    public $canvas_form;
    public $carousel_item;
    public $canvas_item;
    public $chapterIndex;
    public $js_params = [];
    public $params = [
    ];

    public $sequence;
    public $nextVideo;
    public $prevVideo;
    public $nextSequence;
    public $prevSequence;
    public $VideoDb;

    public function __construct()
    {
        global $db;
        $this->db = $db;
        $this->js_params['COMMENT'] = '//';
        $this->videoId();
        $this->playlistId();
        $this->VideoDb = new VideoDb();
    }

    public function getCurrentVideoid()
    {
        $this->db->where('video_id', $this->id);
        $user = $this->db->getOne(Db_TABLE_SEQUENCE);
        $this->sequence = $user['seq_id'];

        return $user['seq_id'];
    }

    public function getVideoSeq($type)
    {
        $user = $this->db->rawQueryOne('select '.$type.'(seq_id) as cnt from '.Db_TABLE_SEQUENCE.' where Library = ?', [$_SESSION['library']]);
        return $user['cnt'];
    }

    public function getPrevVideo()
    {
        $seq = $this->getCurrentVideoid();
        $min = $this->getVideoSeq('MIN');
        $max = $this->getVideoSeq('MAX');
        $res = null;
        do {
            --$seq;
            $this->db->where('seq_id', $seq);
            $this->db->where('Library', $_SESSION['library']);
            $res = $this->db->getone(Db_TABLE_SEQUENCE, '*');
            if ($seq <= $min) {
                $seq = $max + 1;
            }
        } while (null === $res);
        $this->prevVideo = $res['video_id'];
        $this->prevSequence = $seq;

        return $this->prevVideo;
    }

    public function getNextVideo()
    {
        $seq = $this->getCurrentVideoid();
        $min = $this->getVideoSeq('MIN');
        $max = $this->getVideoSeq('MAX');
        $res = null;

        do {
            ++$seq;
            $this->db->where('seq_id', $seq);
            $this->db->where('Library', $_SESSION['library']);
            $res = $this->db->getone(Db_TABLE_SEQUENCE, '*');

            if ($seq >= $max) {
                $seq = $min - 1;
            }
        } while (null === $res);
        $this->nextVideo = $res['video_id'];
        $this->nextSequence = $seq;

        return $this->nextVideo;
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
        // $fileList = (new FileListing(new Request()));

        $res = $this->VideoDb->getVideoDetails($this->id);
        dump($res);

        if (!isset($this->playlist_id)) {
            $this->js_params['NEXT_VIDEO_ID'] = $this->getNextVideo();
            $this->js_params['PREV_VIDEO_ID'] = $this->getPrevVideo();
            $this->js_params['COMMENT'] = '';

            $txt = 'Prev: '.$this->prevVideo.':'.$this->prevSequence.' -- ';
            $txt .= 'Current: '.$this->id.':'.$this->sequence.' -- ';
            $txt .= 'Next: '.$this->nextVideo.':'.$this->nextSequence.'  ';

            
        }

        $result = $res[0];
        $active_title = null;

        if (null === $active_title) {
            $active_title = $result['filename'];
        }

        $fullpath = str_replace(__PLEX_LIBRARY__, APP_HOME.'/videos', $result['fullpath']);
        $video_file = $fullpath.'/'.$result['filename'];

        $this->params['PAGE_TITLE'] = $result['title'];
        $this->params['VideoId'] = $this->id;

        $this->params['thumbnail'] = APP_HOME.$result['thumbnail'];

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
        $this->js_params['height'] = $result['height'];
        $this->js_params['width'] = $result['width'];

        $this->js_params['VideoStudio'] = $result['studio'];
        $this->js_params['VideoTitle'] = $active_title;
        $this->js_params['VideoArtist'] = $result['artist'];
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
                Functions::$PlaylistDir.'/canvas/form',
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
            Functions::$PlaylistDir.'/'.$type.'/item',
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

      $results = $this->VideoDb->getPlaylistVideos($this->playlist_id);
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
        $max = count($results);

        for ($i = 0; $i < $max; ++$i) {
            $class = '';
            if ($this->id == $results[$i]['playlist_video_id']) {
                $class = ' active';
            }

            $this->canvas_item .= $this->getPlaylistItem($results[$i], $class, 'canvas');

            if (' active' == $class) {

                $indx = $i + 1;
                if (\array_key_exists($indx, $results)) {
                    $next_video_id = $results[$indx]['playlist_video_id'];
                } else {
                    $next_video_id = $results[0]['playlist_video_id'];
                }

                $prev_video_id = $results[$max -1]['playlist_video_id'];
            }

        }
        $this->params['RemoveVideo'] = $this->getRemoveVideo();
        $this->params['CANVAS_HTML'] = $this->getCanvas();

        $this->js_params['PLAYLIST_ID'] = $this->playlist_id;
        $this->js_params['NEXT_VIDEO_ID'] = $next_video_id;
        $this->js_params['PREV_VIDEO_ID'] = $prev_video_id;
        $this->js_params['COMMENT'] = '';
    }



    public function getCanvas()
    {
        $this->addSearchBox();

        return Render::html(Functions::$PlaylistDir.'/canvas/block', ['CANVAS_LIST' => $this->canvas_item,
            'PlaylistName' => $this->playlistName, 'Canvas_Form' => $this->canvas_form]);
    }

    public function videoJs()
    {
        return Render::javascript($this->videoTemplate.'/video_js', $this->js_params);
    }

    public function getRemoveVideo()
    {
        $videoId = Elements::add_hidden('videoId', $this->id);
        $videoId .= Elements::add_hidden('playlistid', $this->playlist_id);

        return Render::html(Functions::$ButtonDir.'/remove', ['HIDDEN_VIDEO_ID' => $videoId]);
    }

    public function addChapter()
    {
        $videoId = Elements::add_hidden('videoId', $this->id);
        if (null != $this->playlist_id) {
            $videoId .= Elements::add_hidden('playlistid', $this->playlist_id);
        }

        return Render::html(Functions::$ChapterDir.'/addChapter', ['HIDDEN_VIDEO_ID' => $videoId]);
    }

    public function getChapterButtons()
    {
        $index = $this->getChapters();
        foreach ($index as $i => $row) {
            // $editableClass = 'edit'.$row['time'];
            // $functionName = 'make'.$row['time'].'Editable';

            // $row['EDITABLE'] = $editableClass;

            // $this->params['VIDEOINFO_EDIT_JS'] .= Render::javascript(
            //     Functions::$ChapterDir.'/chapter',
            //     [
            //         'ID_NAME' => $row['time'],
            //         'EDITABLE' => $editableClass,
            //         'FUNCTION' => $functionName,
            //         'VIDEO_KEY' => $this->id,
            //     ]
            // );
             $html .= Render::html(Functions::$ChapterDir.'/chapter', $row);
        }

        return $html;
    }

    public function getChapterJson()
    {
        return json_encode($this->getChapters());
    }

    public function getChapters()
    {
        if (null == $this->chapterIndex) {
            $this->db->where('video_id', $this->id);
            $this->db->orderBy('timeCode', 'ASC');
            $search_result = $this->db->get(Db_TABLE_VIDEO_CHAPTER);
            foreach ($search_result as $i => $row) {
                if (null === $row['name']) {
                    $row['name'] = 'Timestamp';
                }

                $this->chapterIndex[] = ['time' => $row['timeCode'], 'label' => $row['name']];
            }
        }

        return $this->chapterIndex;
    }
}
