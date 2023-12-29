<?php
/**
 * plex web viewer
 */

namespace Plex\Core;

use Camoo\Config\Config;

class PlexLoader
{
    private ?Config $config = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->setupTables();
       // dd(get_defined_constants(true));
    }

    private function setupTables()
    {
        $tableList = $this->config['database'];
        $prefix    = $this->config['database']['TABLE_PREFIX'];
        unset($tableList['TABLE_PREFIX']);
        foreach ($tableList as $key => $table) {
            $constName = 'Db_TABLE_'.$key;
            \define($constName, $prefix.'_'.$table);
        }
    }
}
