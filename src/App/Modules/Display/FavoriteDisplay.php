<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Display;

use Plex\Template\Functions\Functions;
use Plex\Template\Render;

class FavoriteDisplay
{
    private static function favButton($videoid, $button)
    {
        $params['FavoriteButton'] = $button;
        $params['FavBtnId']       = '_'.$videoid;

        return Render::html(Functions::$ButtonDir.'/Favorite/button', $params);
    }

    public static function RemoveFavoriteVideo($videoid= null)
    {
        $button = Render::html(Functions::$ButtonDir.'/Favorite/remove', ['videoId' => ','.$videoid]);

        return self::favButton($videoid, $button);
    }

    public static function addFavoriteVideo($videoid = null)
    {
        $button = Render::html(Functions::$ButtonDir.'/Favorite/add', ['videoId' => ','.$videoid]);

        return self::favButton($videoid, $button);
        //        return Render::html(Functions::$ButtonDir.'/add');
    }
}
