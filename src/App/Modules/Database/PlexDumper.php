<?php

namespace Plex\Modules\Database;

use Ifsnop\Mysqldump as IMysqldump;
use Ifsnop\Mysqldump\Mysqldump;

class PlexDumper extends IMysqldump
{
    public function __construct(
        $dsn = '',
        $user = '',
        $pass = '',
        $dumpSettings = [],
        $pdoSettings = []
    ) {
        $dumpSettingsDefault = [
            'include-tables' => [],
            'exclude-tables' => [],
            'compress' => Mysqldump::NONE,
            'init_commands' => [],
            'no-data' => [
                Db_TABLE_VIDEO_FILE,
                Db_TABLE_VIDEO_INFO,
                Db_TABLE_VIDEO_CUSTOM,
                Db_TABLE_VIDEO_METADATA,
                Db_TABLE_SEARCH_DATA,
                Db_TABLE_PLAYLIST_VIDEOS,
                Db_TABLE_PLAYLIST_DATA,
                Db_TABLE_FAVORITE_VIDEOS, ],
            'if-not-exists' => false,
            'reset-auto-increment' => true,
            'add-drop-database' => false,
            'add-drop-table' => true,
            'add-drop-trigger' => true,
            'add-locks' => true,
            'complete-insert' => false,
            'databases' => false,
            'default-character-set' => Mysqldump::UTF8,
            'disable-keys' => true,
            'extended-insert' => true,
            'events' => false,
            'hex-blob' => true, /* faster than escaped content */
            'insert-ignore' => false,
            'net_buffer_length' => self::MAXLINESIZE,
            'no-autocommit' => true,
            'no-create-db' => false,
            'no-create-info' => false,
            'lock-tables' => true,
            'routines' => true,
            'single-transaction' => true,
            'skip-triggers' => false,
            'skip-tz-utc' => false,
            'skip-comments' => true,
            'skip-dump-date' => false,
            'skip-definer' => false,
            'where' => '',
            /* deprecated */
            'disable-foreign-keys-check' => true,
        ];

        $pdoSettingsDefaults = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
        ];
        $this->_pdoSettings = self::array_replace_recursive($pdoSettingsDefault, $pdoSettings);
        $this->_dumpSettings = self::array_replace_recursive($dumpSettingsDefault, $dumpSettings);
    }
}
