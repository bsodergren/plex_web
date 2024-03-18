<?php

use Plex\Modules\Database\PlexSql;


define('DB_DATABASE', $_ENV['DB_DATABASE']);

define('DB_HOST', $_ENV['DB_HOST']);

define('DB_USERNAME', $_ENV['DB_USER']);

define('DB_PASSWORD', $_ENV['DB_PASS']);

define('Db_MEDIATAG_PREFIX', 'mediatag_');
define('Db_TABLE_VIDEO_FILE', Db_MEDIATAG_PREFIX.'video_file');
define('Db_TABLE_VIDEO_INFO', Db_MEDIATAG_PREFIX.'video_info');
define('Db_TABLE_VIDEO_CUSTOM', Db_MEDIATAG_PREFIX.'video_custom');
define('Db_TABLE_VIDEO_TAGS', Db_MEDIATAG_PREFIX.'video_metadata');
define('Db_TABLE_STUDIO', Db_MEDIATAG_PREFIX.'studios');
define('Db_TABLE_GENRE', Db_MEDIATAG_PREFIX.'genre');
define('Db_TABLE_ARTISTS', Db_MEDIATAG_PREFIX.'artists');
define('Db_TABLE_TAGS', Db_MEDIATAG_PREFIX.'tags');
define('Db_TABLE_KEYWORD', Db_MEDIATAG_PREFIX.'keyword');
define('Db_TABLE_TITLE', Db_MEDIATAG_PREFIX.'title');

define('Db_PLEXWEB_PREFIX', 'plexweb_');
define('Db_TABLE_VIDEO_CHAPTER', Db_PLEXWEB_PREFIX.'video_chapter');
define('Db_TABLE_SETTINGS', Db_PLEXWEB_PREFIX.'settings');
define('Db_TABLE_SEARCH_DATA', Db_PLEXWEB_PREFIX.'search_data');
define('Db_TABLE_SEQUENCE', Db_PLEXWEB_PREFIX.'sequence');
define('Db_TABLE_PLAYLIST_VIDEOS', Db_PLEXWEB_PREFIX.'playlist_videos');
define('Db_TABLE_PLAYLIST_DATA', Db_PLEXWEB_PREFIX.'playlist_data');
define('Db_TABLE_SMARTLIST_DATA', Db_PLEXWEB_PREFIX.'smartlist_data');
define('Db_TABLE_WORDMAP', Db_PLEXWEB_PREFIX.'wordMap');


$db = new PlexSql(); // ('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$sql = 'DELETE FROM '.Db_TABLE_SEARCH_DATA.' WHERE updatedAt < NOW() - INTERVAL 8 HOUR';
$res = $db->rawQuery($sql);
