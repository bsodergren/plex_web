<?php

namespace Plex\Modules\Process;

use Plex\Modules\Process\Traits\DbWrapper;

class Info
{
    use DbWrapper;
    public $tagValue;
    public $video_key;

    public function __construct()
    {
        global $db;
        $this->db = $db;
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
        if ('null' == $this->tagValue) {
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
        //  dd($this->getLastQuery());
    }

    public function save($tag, $data)
    {
        // dump($tag, $data);
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
}
