<?php
/**
 * plex web viewer
 */

use Plex\Core\Request;
use Plex\Core\RoboLoader;
global $_SESSION;

$include_array =  RoboLoader::get_filelist(__PHP_INC_CORE_DIR__, 'php', 1);

foreach ($include_array as $required_file) {
    require_once $required_file;
}

$r = new Request();
$uri = $r->getURI();
$urlPattern = $r->geturlPattern();
$url_array = $r->url_array();
$currentPage = $r->currentPage;
