<?php

use Plex\Template\Functions\Functions;
use Plex\Template\HTML\Elements;
use Plex\Template\Render;


/**
 * plex web viewer.
 */

require_once '_config.inc.php';

$table_body_html = '';
$main_links = '';

$sql = 'SELECT * FROM '.Db_TABLE_SMARTLIST_DATA;
$results = $db->query($sql);

utmdump($results);

define('TITLE', 'Home');
define('GRID_VIEW', 1);
Render::Display('');
