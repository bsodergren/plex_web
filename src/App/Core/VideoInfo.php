<?php
namespace Plex\Core;
/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */
class VideoInfo
{
    public $tagValue;
    public $video_key;

    public function __construct() {}

    public function __call($method, $args)
    {
        // dump(['call', $method, $args]);
        $this->tagValue  = $args[0];
        $this->video_key = $args[1];
        $this->update($method);
    }

    public function update($tag)
    {
        global $db;
        // dump(['V update', $tag, $this->tagValue, $this->video_key]);

        $data['video_key'] = $this->video_key;
        $data[$tag]        = $this->tagValue;

        if ('null' == $this->tagValue) {
            $data[$tag] = null;

            $query      = 'UPDATE `metatags_video_custom` SET ';
            $query .= ' `'.$tag."` = NULL WHERE `metatags_video_custom`.`video_key` = '".$this->video_key."'";
            // dump($query);
            $db->rawQuery($query);
        } else {
            $fieldArray = $data;
            if (array_key_exists('video_key', $fieldArray)) {
                unset($fieldArray['video_key']);
            }
            $db->onDuplicate($fieldArray, 'id');
            $db->insert(Db_TABLE_VIDEO_CUSTOM, $data);
        }
        // dump($db->getLastQuery());
    }

    public function save($tag, $data)
    {
        // dump($tag, $data);
    }

    public function updateRating($video_id, $rating)
    {
        global $db;

        if ('' == $rating) {
            $rating = 0;
        }
        $data = [
            'rating' => $rating,
        ];

        $db->where('id', $video_id);
        $db->update(Db_TABLE_VIDEO_FILE, $data);
        //        dd(["Fdsa",$video_id,$rating]);
    }
}
