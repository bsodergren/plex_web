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

        $sql .= ' FROM metatags_video_file f ';
        $sql .= ' INNER JOIN metatags_video_metadata m on f.video_key=m.video_key '; // .PlexSql::getLibrary();
        $sql .= ' LEFT JOIN metatags_video_custom c on m.video_key=c.video_key ';
        $sql .= ' LEFT OUTER JOIN metatags_video_info i on f.video_key=i.video_key ';
        $sql .= " WHERE f.filename = '".$filename."'";
        return $this->db->query($sql);
    }


}