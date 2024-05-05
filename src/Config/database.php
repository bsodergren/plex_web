<?php

use Camoo\Config\Config;
use Camoo\Config\Enum\Parser;
use Camoo\Config\Parser\Yaml;
use Plex\Modules\Database\PlexSql;

// $conf = new Config($settingsYaml, Parser::YAML, true);

define('DB_DATABASE', $_ENV['DB_DATABASE']);

define('DB_HOST', $_ENV['DB_HOST']);

define('DB_USERNAME', $_ENV['DB_USER']);

define('DB_PASSWORD', $_ENV['DB_PASS']);

$dbConfig = $config['path']['mediatag'].\DIRECTORY_SEPARATOR.'config'.\DIRECTORY_SEPARATOR.'database.yaml';
UTM\Utilities\Loader::loadDatabase($dbConfig, 'Db_TABLE_', 'Db_', '');

define('Db_DATA_TABLES', [
    Db_TABLE_SETTINGS,
    // Db_TABLE_WORDMAP,
    Db_TABLE_ARTISTS,
    Db_TABLE_GENRE,
    Db_TABLE_KEYWORD,
    Db_TABLE_STUDIOS,
    Db_TABLE_TAGS,
    Db_TABLE_TITLE,
]);

$db = new PlexSql(); // ('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$sql = 'DELETE FROM '.Db_TABLE_SEARCH_DATA.' WHERE updatedAt < NOW() - INTERVAL 8 HOUR';
$res = $db->rawQuery($sql);
