<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Process\Traits;

use Ifsnop\Mysqldump as IMysqldump;
use Nette\Utils\FileSystem;
use Plex\Modules\Database\PlexSql;
use UTM\Utilities\File;
use UTMTemplate\Template;

trait ExportSQL
{
    private $tableDir = __PHP_SCHEMA_DIR__.'/Tables';
    private $dataDir  = __PHP_SCHEMA_DIR__.'/Data';
    private $dump;

    private $settings = [
        'reset-auto-increment'       => true,
        'skip-definer'               => false,
        'disable-foreign-keys-check' => true,
        'extended-insert'            => false,
        'hex-blob'                   => true, /* faster than escaped content */
        'insert-ignore'              => false,
        'no-autocommit'              => true,
        'lock-tables'                => true,
        'single-transaction'         => false,
        'skip-triggers'              => false,
        'skip-tz-utc'                => false,
        'skip-comments'              => true,
        'routines'                   => false,
    ];
    private $dataSettings = [
        'no-create-db'   => true,
        'no-create-info' => true,
        'skip-dump-date' => false,
    ];

    private $tableSettings = [
        'no-data'        => true,
        'add-drop-table' => true,
        'skip-dump-date' => true,
    ];

    private function connect()
    {
        FileSystem::delete($this->tableDir);
        FileSystem::delete($this->dataDir);

        FileSystem::createDir($this->tableDir);
        FileSystem::createDir($this->dataDir);

        // parent::__construct('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $this->dsn = 'mysql:host=localhost;dbname='.DB_DATABASE;
    }

    private function getTables()
    {
        $res = PlexSql::$DB->query('Show Tables;');
        foreach ($res as $k => $r) {
            $tables[] = $r['Tables_in_plex_web'];
        }

        $this->tables = $tables;
    }

    private function dumpTable($settings, $sqlDir, $table, $callback = null)
    {
        $dump = new IMysqldump\Mysqldump($this->dsn, DB_USERNAME, DB_PASSWORD, $settings);
        if (null !== $callback) {
            $dump->setTransformTableRowHook($callback);
        }

        $table   = str_replace([Db_MEDIATAG_PREFIX, Db_PLEXWEB_PREFIX], ['', ''], $table);
        $sqlFile = $sqlDir.\DIRECTORY_SEPARATOR.$table.'.sql';
        $sqlFile = FileSystem::platformSlashes($sqlFile);
        echo Template::put('Writing '.$table.' to '.$sqlFile);
        $this->sqlFileList[] = $sqlFile;

        $dump->start($sqlFile);
    }

    private function exportTables()
    {
        $settings = array_merge($this->settings, $this->tableSettings);
        foreach ($this->tables as $n => $table) {
            $settings['include-tables'] = [$table];
            $this->dumpTable($settings, $this->tableDir, $table);
        }

        unset($settings['include-tables']);
        $settings['exclude-tables'] = $this->tables;
        $settings['routines']       = true;

        $this->dumpTable($settings, $this->tableDir, 'extra');
    }

    private function exportData()
    {
        $settings = array_merge($this->settings, $this->dataSettings);
        foreach (Db_DATA_TABLES as $n => $table) {
            $settings['include-tables'] = [$table];
            $this->dumpTable($settings, $this->dataDir, $table);
        }

        $table                      = 'sequence';
        $settings['include-tables'] = [$table];
        $this->dumpTable($settings, $this->dataDir, $table, function ($tableName, array $row) {
            if ('sequence' === $tableName) {
                $row['cur_value'] = 0;
            }

            return $row;
        });
    }

    public function exportSql()
    {
        $this->getTables();
        $this->connect();
        $this->exportTables();
        $this->exportData();

        $replace = [
            'COLLATE=utf8mb4_general_ci' => '',
            'COLLATE=utf8_general_ci'    => '',
        ];
        foreach ($replace as $s => $r) {
            $search[]       = $s;
            $search[]       = str_replace('=', ' ', $s);
            $replacements[] = $r;
            $replacements[] = $r;
        }

        $search[]       = Db_MEDIATAG_PREFIX;
        $search[]       = Db_PLEXWEB_PREFIX;
        $replacements[] = '%%MEDIATAG_PREFIX%%';
        $replacements[] = '%%PLEXWEB_PREFIX%%';
        foreach ($this->sqlFileList as $k => $file) {
            File::replace($file, $search, $replacements);
        }
        // dd($this->sqlFileList);
        $this->myHeader(__URL_HOME__);
    }
}
