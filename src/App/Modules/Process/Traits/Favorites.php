<?php
namespace Plex\Modules\Process\Traits;

use Plex\Modules\Database\FavoriteDB;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;

trait Favorites
{

    public function addFavorite()
    {
        if(!array_key_exists('video_id',$_REQUEST)){
            return "";
        }
        FavoriteDB::add($_REQUEST['videoId']);
        echo Render::html(Functions::$ButtonDir.'/Favorite/remove');
        exit;
    }

    public function RemoveFavorite()
    {
        if(!array_key_exists('video_id',$_REQUEST)){
            return "";
        }
        FavoriteDB::delete($_REQUEST['videoId']);
        echo Render::html(Functions::$ButtonDir.'/Favorite/add');
        exit;

    }

    public function isFavorite()
    {
        if(!array_key_exists('video_id',$_REQUEST)){
            return "";
        }
       $res = FavoriteDB::get($_REQUEST['videoId']);
       if($res){
           echo Render::html(Functions::$ButtonDir.'/Favorite/remove');
           exit;
       }
       echo Render::html(Functions::$ButtonDir.'/Favorite/add');
        exit;

    }
}
