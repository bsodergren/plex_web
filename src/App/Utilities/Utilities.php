<?php
/**
 * plex web viewer
 */

namespace Plexweb\Utilities;

// Command like Metatag writer for video files.

use Nette\Utils\Arrays;

class Utilities
{
    public static function matcharray($array, $string)
    {
        if (!Arrays::contains($array, $string)) {
            return false;
        }

        return true;
    }
}
