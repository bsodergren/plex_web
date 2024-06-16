<?php
/**
 *  Plexweb
 */

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Display\VideoDisplay;

/**
 * plex web viewer.
 */

require_once '_config.inc.php';

$sql    = PlexSql::query_builder(Db_TABLE_VIDEO_METADATA, 'studio,subLibrary,count(video_key) as cnt', 'library', 'studio,subLibrary', 'studio,subLibrary ASC');

$result = $db->query($sql);

$homePage = (new VideoDisplay('Library'))->init('Home');

$homePage->Display($result);
