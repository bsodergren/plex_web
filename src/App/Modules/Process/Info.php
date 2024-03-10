<?php

namespace Plex\Modules\Process;

use Nette\Utils\FileSystem;
use Plex\Modules\Process\Traits\DbWrapper;

class Info
{
    use DbWrapper;
    public $tagValue;
    public $video_key;
    private $postArray;

    public function __construct($req)
    {
        global $db;
        $this->db = $db;
        $this->postArray = $req;
    }

    public function __call($method, $args)
    {
        $this->tagValue = $args[0];
        $this->video_key = $args[1];
        $this->InfoUpdate($method);
    }

    public function InfoUpdate($tag)
    {
        $data['video_key'] = $this->video_key;
        $data[$tag] = $this->tagValue;
        if ('NULL' == $this->tagValue) {
            $data[$tag] = null;

            $query = 'UPDATE `metatags_video_custom` SET ';
            $query .= ' `'.$tag."` = NULL WHERE `metatags_video_custom`.`video_key` = '".$this->video_key."'";

            $this->rawQuery($query);
        } else {
            $fieldArray = $data;
            if (\array_key_exists('video_key', $fieldArray)) {
                unset($fieldArray['video_key']);
            }
            $this->onDuplicate($fieldArray, 'id');
            $this->insert(Db_TABLE_VIDEO_CUSTOM, $data);
        }
        //  utmdd($this->getLastQuery());
    }

    public function save($tag, $data)
    {
        utmdump($tag, $data);
    }

    public function updateRating($video_id, $rating)
    {
        if ('' == $rating) {
            $rating = 0;
        }
        $data = [
            'rating' => $rating,
        ];
        $this->where('id', $video_id);
        $this->update(Db_TABLE_VIDEO_FILE, $data);
    }

    public function deleteFile()
    {
        $this->where('id', $this->postArray['id']);
        $res = $this->getOne(Db_TABLE_VIDEO_FILE, ['fullpath', 'filename']);
        $file = $res['fullpath'].\DIRECTORY_SEPARATOR.$res['filename'];
        FileSystem::delete($file);
        $this->where('id', $this->postArray['id']);
        $this->delete(Db_TABLE_VIDEO_FILE);
        $res = $this->where('playlist_video_id', $id)->delete(Db_TABLE_PLAYLIST_VIDEOS);

        return true;
    }
}
