<?php

define('DB_DATABASE', $_ENV['DB_DATABASE']);

define('DB_HOST', $_ENV['DB_HOST']);

define('DB_USERNAME', $_ENV['DB_USER']);

define('DB_PASSWORD', $_ENV['DB_PASS']);

define('Db_TABLE_PREFIX', 'metatags_');


define('Db_TABLE_VIDEO_FILE', Db_TABLE_PREFIX.'video_file');
define('Db_TABLE_VIDEO_INFO', Db_TABLE_PREFIX.'video_info');
define('Db_TABLE_VIDEO_CUSTOM', Db_TABLE_PREFIX.'video_custom');
define('Db_TABLE_VIDEO_TAGS', Db_TABLE_PREFIX.'video_metadata');

define('Db_TABLE_STUDIO', Db_TABLE_PREFIX.'studios');
define('Db_TABLE_GENRE', Db_TABLE_PREFIX.'genre');
define('Db_TABLE_ARTISTS', Db_TABLE_PREFIX.'artists');

define('Db_TABLE_SETTINGS', Db_TABLE_PREFIX.'settings');

define('Db_TABLE_SEARCH_DATA', Db_TABLE_PREFIX.'search_data');

define('Db_TABLE_PLAYLIST_VIDEOS', Db_TABLE_PREFIX.'playlist_videos');
define('Db_TABLE_PLAYLIST_DATA', Db_TABLE_PREFIX.'playlist_data');

define('Db_TABLE_SMARTLIST_DATA', Db_TABLE_PREFIX.'smartlist_data');
