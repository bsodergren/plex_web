<?php
session_start();

if (!defined('APP_AUTHENTICATION'))				DEFINE("APP_AUTHENTICATION",TRUE);
if (!defined('APP_HOME'))						DEFINE("APP_HOME","/plex_web");
if (!defined('APP_PATH'))						DEFINE("APP_PATH", $_SERVER['DOCUMENT_ROOT'] . APP_HOME );

require_once("/home/bjorn/mysql_pwd.php");
DEFINE("__SQL_DB__", "pornhub_2");

if (!defined('DB_DATABASE'))                    define('DB_DATABASE', __SQL_DB__);
if (!defined('DB_HOST'))                        define('DB_HOST','127.0.0.1');
if (!defined('DB_USERNAME'))                    define('DB_USERNAME',__SQL_USER__);
if (!defined('DB_PASSWORD'))                    define('DB_PASSWORD' ,__SQL_PASSWD__);
if (!defined('DB_PORT'))                        define('DB_PORT' ,'');

if (!defined('Db_TABLE_PREFIX'))                   	define('Db_TABLE_PREFIX', "metatags" . "_" );

DEFINE("__LOG_ERRORS__", 1);
DEFINE("__HTML_POPUP__", 0);


if (!defined('__PHP_ASSETS_DIR__'))			    DEFINE("__PHP_ASSETS_DIR__", APP_PATH."/assets");
if (!defined('__RUNTIME_CONFIG__'))			    DEFINE("__RUNTIME_CONFIG__", __PHP_ASSETS_DIR__."/.config");


require_once(__PHP_ASSETS_DIR__."/header.inc.php");


?>
