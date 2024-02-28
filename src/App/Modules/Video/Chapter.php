<?php

namespace Plex\Modules\Video;

use Plex\Template\Render;

class Chapter
{
    public object $db;

    public $data;
    public $library;
    public $playlist_id;
    public $id;
    public $chapterTemplate = 'elements/Chapters';

    public function __construct($data)
    {
        global $db,$_SESSION;
        $this->data = $data;
        $this->db = $db;
        $this->library = $_SESSION['library'];
        if (isset($data['playlist_id'])) {
            $this->playlist_id = $data['playlist_id'];
        }
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
    }

    public function addChapter()
    {
        $videoId = Elements::add_hidden('videoId', $this->id);
        if (null != $this->playlist_id) {
            $videoId .= Elements::add_hidden('playlistid', $this->playlist_id);
        }

        return Render::html($this->chapterTemplate . '/addChapter', ['HIDDEN_VIDEO_ID' => $videoId]);
    }


    public function getChapterJson()
    {
        return json_encode($this->Chapters->getChapters());
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

    public function addChapterVideo()
    {
        $data = [
            'timeCode' => $this->data['timeCode'],
            'video_id' => $this->data['videoId'],
            'name' => $this->data['name'],
        ];
        $res = $this->db->insert(Db_TABLE_VIDEO_CHAPTER, $data);
        $urlQuery = '?id='.$this->data['videoId'];
        if (\array_key_exists('playlistid', $this->data)) {
            $urlQuery .= '&playlist_id='.$this->data['playlistid'];
        }
return $this->data['timeCode'];
        // return __URL_HOME__.'/video.php'.$urlQuery;
    }

    public function updateChapter()
    {
        $timeCode = null;
        foreach ($this->data as $key => $value) {
            if (\is_int($key)) {
                $timeCode = $key;
                $name = $value;
                continue;
            }
            if ('video_key' == $key) {
                $videoId = $value;
                continue;
            }
        }
        $sql = 'UPDATE '.Db_TABLE_VIDEO_CHAPTER." SET name = '".$name."' WHERE video_id = ".$videoId.' and timeCode = '.$timeCode.'';
        $this->db->query($sql);
    }

    public function getChapterButtons()
    {
        $index = $this->getChapters();
        foreach ($index as $i => $row) {
            $editableClass = 'edit'.$row['time'];
            $functionName = 'make'.$row['time'].'Editable';

            $row['EDITABLE'] = $editableClass;

            $row['VIDEOINFO_EDIT_JS'] = Render::javascript(
                $this->chapterTemplate.'/chapter',
                [
                    'ID_NAME' => $row['time'],
                    'EDITABLE' => $editableClass,
                    'FUNCTION' => $functionName,
                    'VIDEO_KEY' => $this->id,
                ]
            );
            $html .= Render::html($this->chapterTemplate.'/chapter', $row);
        }

        return $html;
    }
}
