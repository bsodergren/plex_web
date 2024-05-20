<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Process\Traits;

use Plex\Modules\Database\FavoriteDB;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;

trait Favorites
{

    public function addFavorite()
    {
        if (!\array_key_exists('videoId', $_REQUEST)) {
            return '';
        }
        FavoriteDB::add($_REQUEST['videoId']);
        echo Render::html(Functions::$ButtonDir.'/Favorite/remove',['videoId' => ','.$_REQUEST['videoId']]);
    }

    public function RemoveFavorite()
    {
      //  utmdump([__METHOD__, $_REQUEST]);

        if (!\array_key_exists('videoId', $_REQUEST)) {
            return '';
        }
        FavoriteDB::delete($_REQUEST['videoId']);
        echo  Render::html(Functions::$ButtonDir.'/Favorite/add',['videoId' =>  ','.$_REQUEST['videoId']]);
    }

    public function isFavorite()
    {
        utmdump([__METHOD__, $_REQUEST]);

        if (!\array_key_exists('videoId', $_REQUEST)) {
            return '';
        }
        $res = FavoriteDB::get($_REQUEST['videoId']);
        if ($res) {
            echo Render::html(Functions::$ButtonDir.'/Favorite/remove');
        }
        echo Render::html(Functions::$ButtonDir.'/Favorite/add');
    }
}
