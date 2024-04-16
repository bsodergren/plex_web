<?php

namespace Plex\Modules\Database;

use Plex\Modules\Database\FavoriteDB;
use Plex\Modules\Database\Traits\VideoLookup;

class VideoDb
{

    use VideoLookup;

    public static $VideoMetaFields =
    ['COALESCE (c.title,m.title) as title ',
        'COALESCE (c.artist,m.artist) as artist ',
        'COALESCE (c.genre,m.genre) as genre ',
        'COALESCE (c.studio,m.studio) as studio ',
        'COALESCE (c.substudio,m.substudio) as substudio ',
        'COALESCE (c.keyword,m.keyword) as keyword '];

    public static $VideoInfoFields = ['i.format', 'i.bit_rate', 'i.width', 'i.height'];

    public static $VideoFileFields = ['v.rating',
        'v.filename', 'v.fullpath', 'v.library',
        'v.duration', 'v.filesize', 'v.added', 'v.id', 'v.video_key', 'v.thumbnail', 'v.preview'];
    public static $PlayListFields = ['p.playlist_video_id', 'p.playlist_id'];
    public static $FavoriteFields = ['f.video_id as favorite_id'];

    public $db;
    public $library ;
    public $id;

    public function __construct($library = null)
    {
        $this->db = PlexSql::$DB;
        $this->library  = $_SESSION['library'];
        if ($library !== null)
        {
            $this->library = $library;
        }

        if (\array_key_exists('id', $_REQUEST)) {
            $this->id = $_REQUEST['id'];
        }
    }

    public function videoId()
    {
        return $this->id;
    }

    public static function getVideoJoins()
    {
    }

    public function getVideoDetails($id)
    {
        $sql = self::getVideoQuery();
        $sql .= " WHERE v.id = '".$id."'";
        FavoriteDB::get($id);
        return $this->db->query($sql);
    }

    public function getVideoPath($id)
    {
        $sql = 'SELECT concat(fullpath,"/",filename) as file FROM '.Db_TABLE_VIDEO_FILE.' WHERE id = '.$id;
        $results = $this->db->query($sql);

        return $results[0]['file'];
    }
}
