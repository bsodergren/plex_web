<?php
namespace Plex\Core\Settings;
/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */
class Settings
{
    public static function isTrue($define_name)
    {
        if (defined($define_name)) {
            if (true == constant($define_name)) {
                //  mediaUpdate::echo(constant($define_name));
                return 1;
            }
        }

        return 0;
    }

    public static function isSet($define_name)
    {
        if (defined($define_name)) {
            return 1;
        }

        return 0;
    }
}