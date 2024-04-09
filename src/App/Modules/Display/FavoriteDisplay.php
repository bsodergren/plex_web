<?php

namespace Plex\Modules\Display;

use Plex\Template\Functions\Functions;
use Plex\Template\Render;

class FavoriteDisplay
{
    public static function RemoveFavoriteVideo($videoid= null)
    {
        return Render::html(
            Functions::$ButtonDir.'/Favorite/button',
            ['FavoriteButton' => Render::html(Functions::$ButtonDir.'/Favorite/remove',
        ['videoId' => ','.$videoid]),
        'FavBtnId' => '_'.$videoid]);
    }

    public static function addFavoriteVideo($videoid = null)
    {
        return Render::html(Functions::$ButtonDir.'/Favorite/button',
        ['FavoriteButton' => Render::html(Functions::$ButtonDir.'/Favorite/add',
        ['videoId' => ','.$videoid]),
        'FavBtnId' => '_'.$videoid]);
        //        return Render::html(Functions::$ButtonDir.'/add');
    }
}
