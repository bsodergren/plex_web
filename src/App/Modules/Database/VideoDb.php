<?php

namespace Plex\Modules\Database;

use Plex\Modules\Database\PlexSql;

class VideoDb
{
    public static $VideoMetaFields =
    ['COALESCE (c.title,m.title) as title ',
        'COALESCE (c.artist,m.artist) as artist ',
        'COALESCE (c.genre,m.genre) as genre ',
        'COALESCE (c.studio,m.studio) as studio ',
        'COALESCE (c.substudio,m.substudio) as substudio ',
        'COALESCE (c.keyword,m.keyword) as keyword '];

    public static $VideoInfoFields = ['i.format', 'i.bit_rate', 'i.width', 'i.height'];

    public static $VideoFileFields = ['f.rating',
        'f.filename', 'f.fullpath', 'f.library',
        'f.duration', 'f.filesize', 'f.added', 'f.id', 'f.video_key', 'f.thumbnail', 'f.preview'];
    public static $PlayListFields = ['p.playlist_video_id', 'p.playlist_id'];

    public function __construct()
    {
        $this->db = PlexSql::$DB;
    }
    public function videoId()
    {
        if (\array_key_exists('id', $_REQUEST)) {
            $this->id = $_REQUEST['id'];
        }

        return $this->id;
    }
    public static function getVideoJoins()
    {

    }



    public function getVideoDetails($id)
    {
        $fieldArray = array_merge(self::$VideoMetaFields, self::$VideoInfoFields, self::$VideoFileFields);

        $sql = 'SELECT ';
        $sql .= implode(',', $fieldArray);

        $sql .= ' FROM '.Db_TABLE_VIDEO_FILE.' f ';
        $sql .= ' INNER JOIN '.Db_TABLE_VIDEO_TAGS.' m on f.video_key=m.video_key '; // .PlexSql::getLibrary();
        $sql .= ' LEFT JOIN '.Db_TABLE_VIDEO_CUSTOM.' c on m.video_key=c.video_key ';
        $sql .= ' LEFT OUTER JOIN '.Db_TABLE_VIDEO_INFO.' i on f.video_key=i.video_key ';
        $sql .= " WHERE f.id = '".$id."'";
        return $this->db->query($sql);
    }

    public function getPlaylistVideos($playlist_id)
    {
        $fieldArray = array_merge(self::$VideoMetaFields, self::$VideoFileFields, self::$PlayListFields);

        $sql = 'SELECT ';
        $sql .= implode(',', $fieldArray);
        $sql .= ' FROM '.Db_TABLE_PLAYLIST_VIDEOS.' p ';
        $sql .= ' ,  '.Db_TABLE_VIDEO_FILE.' f  ';
        $sql .= ' INNER JOIN '.Db_TABLE_VIDEO_TAGS.'  m on f.video_key=m.video_key '; // .PlexSql::getLibrary();
        $sql .= ' LEFT JOIN '.Db_TABLE_VIDEO_CUSTOM.'  c on m.video_key=c.video_key ';
        $sql .= ' WHERE  ( p.playlist_id = '.$playlist_id.' and p.playlist_video_id = f.id)';

        return $this->db->query($sql);
    }

    public function getVideoPath($id)
    {
        $sql = 'SELECT concat(fullpath,"/",filename) as file FROM '.Db_TABLE_VIDEO_FILE.' WHERE id = '.$id;
        $results = $this->db->query($sql);
        return $results[0]['file'];

    }

}
