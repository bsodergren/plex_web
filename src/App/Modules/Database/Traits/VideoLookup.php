<?php

namespace Plex\Modules\Database\Traits;

use Plex\Modules\Database\PlexSql;

trait VideoLookup
{
    public static function getVideo($video_id)
    {
        $sql = self::getVideoQuery()." WHERE v.id = '".$video_id."'";
        $results = PlexSql::$DB->query($sql);

        return $results[0];
    }

    public function getVideoDetails($filename)
    {
        $sql = self::getVideoQuery()." WHERE v.filename = '".$filename."'";

        return $this->db->query($sql);
    }

    public static function getVideoKey($video_id)
    {
        $sql = 'SELECT video_key FROM '.Db_TABLE_VIDEO_FILE.' WHERE id = '.$video_id;
        $results = PlexSql::$DB->query($sql);

        return $results[0]['video_key'];
    }

    private static function getVideoQuery()
    {
        $fieldArray = array_merge(self::$VideoMetaFields,
            self::$VideoInfoFields, self::$VideoFileFields, self::$FavoriteFields);

        $sql = 'SELECT ';
        $sql .= implode(',', $fieldArray);

        $sql .= ' FROM '.Db_TABLE_VIDEO_FILE.' v ';
        $sql .= ' INNER JOIN '.Db_TABLE_VIDEO_TAGS.' m on v.video_key=m.video_key '; // .PlexSql::getLibrary();
        $sql .= ' LEFT JOIN '.Db_TABLE_VIDEO_CUSTOM.' c on m.video_key=c.video_key ';
        $sql .= ' LEFT JOIN '.Db_TABLE_FAVORITE_VIDEOS.' f on f.video_id=v.id ';
        $sql .= ' LEFT OUTER JOIN '.Db_TABLE_VIDEO_INFO.' i on v.video_key=i.video_key ';

        return $sql;
    }
}
