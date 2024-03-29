<?php

namespace Plex\Modules\Process;

use Plex\Modules\Process\Traits\DbWrapper;
use Plex\Modules\Database\PlexSql;

class Chapter
{
    public object $db;

    public $data;
    public $library;
    public $playlist_id;
    public $id;
    use DbWrapper;

    public function __construct($data)
    {
        global $_SESSION;
        $this->data = $data;
        $this->db = PlexSql::$DB;
        $this->library = $_SESSION['library'];
        if (isset($data['playlist_id'])) {
            $this->playlist_id = $data['playlist_id'];
        }
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
    }

    public function addChapterVideo()
    {
        $data = [
            'timeCode' => $this->data['timeCode'],
            'video_id' => $this->data['videoId'],
            'name' => $this->data['name'],
        ];
        $res = $this->insert(Db_TABLE_VIDEO_CHAPTER, $data);
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
        $this->query($sql);
    }

}
