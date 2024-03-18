<?php
define('__THIS_FILE__', basename($_SERVER['SCRIPT_FILENAME']));
define('__THIS_PAGE__', basename($_SERVER['SCRIPT_FILENAME'], '.php'));

$recent_page = 'list';

if (__THIS_PAGE__ == 'list' || __THIS_PAGE__ == 'grid') {
    $recent_page = __THIS_PAGE__;
}

define('__RECENT_PAGE__', $recent_page.'.php?sort=f.added&direction=ASC');
define('__PHP_ASSETS_DIR__', $_ENV['WEB_HOME'].'/assets');
define('__PHP_INC_CORE_DIR__', __PLEX_APP_DIR__.'/Includes');

define('__PLEX_LIBRARY__', $_ENV['PLEX_HOME']);
define('__CACHE_DIR', __PLEX_LIBRARY__.'/.cache');

define('APP_HOME', $config['path']['APP_HOME']);
define('APP_HTML_ROOT', rtrim($_SERVER['CONTEXT_DOCUMENT_ROOT'], '/'));
define('APP_PATH', APP_HTML_ROOT.APP_HOME);

define('__ERROR_LOG_DIRECTORY__', APP_HTML_ROOT.'/logs');

define('__TPL_CACHE_DIR__', __PLEX_APP_DIR__.'/var/cache/template/');
define('__RAIN_TEMPLATE_DIR__', __PLEX_APP_DIR__);
define('__DEBUG__', true);

define('__LAYOUT_PATH__', __PHP_ASSETS_DIR__);

define('__HTML_TEMPLATE__', __PLEX_APP_DIR__.'/Layout/template');
define('__METADB_HASH', __CACHE_DIR.'/'.$cache_directory.'/metadb.hash');

define('__ROUTE_NAV__', __PHP_YAML_DIR__.'/navigation.yaml');
