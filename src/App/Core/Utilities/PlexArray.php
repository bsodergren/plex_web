<?php
/**
 *  Plexweb
 */

namespace Plex\Core\Utilities;

use Nette\Utils\Arrays;

class PlexArray
{
    public static function matcharray($array, $string)
    {
        if (!Arrays::contains($array, $string)) {
            return false;
        }

        return true;
    }
}
