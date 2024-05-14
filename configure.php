#!/usr/bin/env php
<?php


use Symfony\Component\Console\Application;

define('__ROOT_DIRECTORY__', realpath(__DIR__));
define('__PLEX_APP_DIR__', __ROOT_DIRECTORY__.'/src');

define('__PHP_CONFIG_DIR__', __PLEX_APP_DIR__.'/Config');

define('__PHP_CMD_DIR__', __PHP_CONFIG_DIR__.'/Cmd');
define('__PHP_YAML_DIR__', __PHP_CONFIG_DIR__.'/Routes');
define('__PHP_SCHEMA_DIR__', __PHP_CONFIG_DIR__.'/Schema');

define('__COMPOSER_LIB__', __ROOT_DIRECTORY__.'/vendor');
set_include_path(get_include_path().\PATH_SEPARATOR.__COMPOSER_LIB__);

require_once __COMPOSER_LIB__.'/autoload.php';

// $app            = require __ROOT_DIRECTORY__.'/bootstrap.php';

define('Db_MEDIATAG_PREFIX','mediatag_');
define('Db_PLEXWEB_PREFIX','plextag_');



$customCommands = require __PHP_CMD_DIR__.'/commands.php';

$application    = new Application('App Name', '1.0');

$application->setCommandLoader($customCommands);

$application->run();
