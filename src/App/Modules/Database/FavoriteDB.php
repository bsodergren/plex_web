<?php
namespace Plex\Modules\Database;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Database\VideoDb;

class FavoriteDB extends VideoDb
{

    public static function get($video_id)
    {
        PlexSql::$DB->where('video_id', $video_id);
        $results = PlexSql::$DB->get(Db_TABLE_FAVORITE_VIDEOS);

        if(count($results) > 0){
            utmdump([__METHOD__, $video_id, $results]);
            return true;
        }
        return false;


    }
    public static function add($video_id)
    {


            $data = [
                'video_id' => $video_id,
                'library' =>  $_SESSION['library'],
            ];
            utmdump([__METHOD__, $data]);
            $ids[] = PlexSql::$DB->insert(Db_TABLE_FAVORITE_VIDEOS, $data);

    }

    public static function delete($video_id)
    {
        $sql = 'delete FROM '.Db_TABLE_FAVORITE_VIDEOS.' WHERE video_id = '.$video_id;
        $results = PlexSql::$DB->query($sql);
    }
}
