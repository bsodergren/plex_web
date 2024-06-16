<?php
namespace Plex\Modules\Database;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Database\VideoDb;

class FavoriteDB extends VideoDb
{


    public static function getFavoriteVideos()
    {
        $fieldArray = array_merge(self::$VideoMetaFields, self::$VideoFileFields, self::$FavoriteFields);

        $sql = 'SELECT ';
        $sql .= implode(',', $fieldArray);
        $sql .= ' FROM '.Db_TABLE_FAVORITE_VIDEOS.' f ';
        $sql .= ' ,  '.Db_TABLE_VIDEO_FILE.' v  ';
        $sql .= ' INNER JOIN '.Db_TABLE_VIDEO_METADATA.'  m on v.video_key=m.video_key '; // .PlexSql::getLibrary();
        $sql .= ' LEFT JOIN '.Db_TABLE_VIDEO_CUSTOM.'  c on m.video_key=c.video_key ';
        $sql .= ' WHERE  ( f.video_id = v.id) AND f.Library = "' .$_SESSION['library'].'"' ;
        utminfo( $sql);

        return PlexSql::$DB->query($sql);
    }
    public static function get($video_id)
    {
        PlexSql::$DB->where('video_id', $video_id);
        $results = PlexSql::$DB->get(Db_TABLE_FAVORITE_VIDEOS);

        if(count($results) > 0){
            return true;
        }
        return false;


    }
    public static function add($video_id)
    {

        $found = self::get($video_id);

        if($found === true) {
            return null;
        }
            $data = [
                'video_id' => $video_id,
                'library' =>  $_SESSION['library'],
            ];
            $ids[] = PlexSql::$DB->insert(Db_TABLE_FAVORITE_VIDEOS, $data);

    }

    public static function delete($video_id)
    {
        $found = self::get($video_id);

        if($found === false) {
            return null;
        }

        $sql = 'delete FROM '.Db_TABLE_FAVORITE_VIDEOS.' WHERE video_id = '.$video_id;
        $results = PlexSql::$DB->query($sql);
    }
}
