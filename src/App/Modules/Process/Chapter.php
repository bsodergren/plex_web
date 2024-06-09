<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Process;

use Plex\Modules\Chapter\Chapter as Chapters;
use Plex\Modules\Database\PlexSql;
use Plex\Modules\Process\Traits\DbWrapper;

class Chapter extends Forms
{
    use DbWrapper;
    public object $db;
    public object $Chapters;
    public $data;
    public $library;
    public $playlist_id;
    public $id;

    public function __construct($data)
    {
        global $_SESSION;
        $this->data    = $data;
        $this->db      = PlexSql::$DB;
        $this->library = $_SESSION['library'];
        if (isset($data['playlist_id'])) {
            $this->playlist_id = $data['playlist_id'];
        }
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        $this->Chapters = new Chapters($data);
    }

    public function getChapterVideos()
    {
        return $this->Chapters->getChapterButtons();
    }

    public function addChapterVideo()
    {
        $data = [
            'timeCode' => $this->data['timeCode'],
            'video_id' => $this->data['videoId'],
            'name'     => $this->data['name'],
        ];
        $res = $this->db->insert(Db_TABLE_VIDEO_CHAPTER, $data);

        return $this->getChapterVideos();
        //     $urlQuery = '?id='.$this->data['videoId'];

        //     if (\array_key_exists('playlistid', $this->data)) {
        //         $urlQuery .= '&playlist_id='.$this->data['playlistid'];
        //     }
        //     //return $this->data['timeCode'];
        //     $url =  __URL_HOME__.'/video.php'.$urlQuery;
        //    // utmdd($url);
        //     echo $this->myHeader($url);
        //     exit;
    }

    public function updateChapter()
    {
        if (\array_key_exists('chapterText', $this->data))
        {
            $chapterText = $this->data['chapterText'];
            if (\array_key_exists('chapterId', $this->data))
            {
                $chapterId = $this->data['chapterId'];
                $sql = 'UPDATE '.Db_TABLE_VIDEO_CHAPTER." SET name = '".$chapterText."' WHERE id = ".$chapterId.'';
                $this->db->query($sql);
            }
        }

        return $this->getChapterVideos();
    }

    public function deleteChapter()
    {
        if (\array_key_exists('chapterId', $this->data))
        {
            $chapterId = $this->data['chapterId'];
            $sql       = 'DELETE FROM '.Db_TABLE_VIDEO_CHAPTER.' WHERE id = '.$chapterId;
            utmdump($sql);
            $this->db->query($sql);
        }

        return $this->getChapterVideos();
    }
}
