<?php

class Settings
{
    public static function isTrue($define_name)
    {
        if (defined($define_name)) {
            if (constant($define_name) == true) {
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
} //end class

class MetaFiledb extends dbObject
{
    protected $dbTable = Db_TABLE_FILEDB;
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
    protected $dbTable = Db_TABLE_FILEINFO;
}
