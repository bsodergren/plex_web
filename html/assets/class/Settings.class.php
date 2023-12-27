<?php
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

class MetaSettings extends dbObject
{
    protected $dbTable = Db_TABLE_SETTINGS;
} // end class

class MetaFiledb extends dbObject
{
    protected $dbTable = Db_TABLE_VIDEO_FILE;
}

class MetaStudio extends dbObject
{
    protected $dbTable = Db_TABLE_STUDIO;
}

class MetaArtist extends dbObject
{
    protected $dbTable = Db_TABLE_ARTISTS;
}

class MetaFileinfo extends dbObject
{
    protected $dbTable = Db_TABLE_VIDEO_INFO;
}
