<?php 
namespace Plex\Modules\Database\Traits;

use Plex\Modules\Database\VideoDb;

trait VideoLookup
{

    
    public function getVideoDetails($filename)
    {
        $fieldArray = array_merge(VideoDb::$VideoMetaFields, VideoDb::$VideoInfoFields, VideoDb::$VideoFileFields);

        $sql = 'SELECT ';
        $sql .= implode(',', $fieldArray);

        $sql .= ' FROM '.Db_TABLE_VIDEO_FILE.' f ';
        $sql .= ' INNER JOIN '.Db_TABLE_VIDEO_TAGS.' m on f.video_key=m.video_key '; // .PlexSql::getLibrary();
        $sql .= ' LEFT JOIN '.Db_TABLE_VIDEO_CUSTOM.' c on m.video_key=c.video_key ';
        $sql .= ' LEFT OUTER JOIN '.Db_TABLE_VIDEO_INFO.' i on f.video_key=i.video_key ';
        $sql .= " WHERE f.filename = '".$filename."'";
        return $this->db->query($sql);
    }


}