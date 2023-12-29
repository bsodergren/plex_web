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
        // dump(["call",$method,$args]);
        $this->tagValue  = $args[0];
        $this->video_key = $args[1];
        $this->update($method);
    }

    public function update($tag)
    {
        global $db;
        // dd(["V update",$tag, $this->tagValue,$this->video_key]);

        $data       = [
            'video_key' => $this->video_key,
            $tag        => $this->tagValue];
        $fieldArray = $data;
        if (array_key_exists('video_key', $fieldArray)) {
            unset($fieldArray['video_key']);
        }
        $db->onDuplicate($fieldArray, 'id');
        $r          = $db->insert(Db_TABLE_VIDEO_CUSTOM, $data);
        dd($r);
    }

    public function save($tag, $data)
    {
        // dump($tag, $data);
    }
}
