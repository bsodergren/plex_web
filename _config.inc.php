<?php

if (!defined('APP_AUTHENTICATION'))				DEFINE("APP_AUTHENTICATION",TRUE);

if (!defined('APP_HOME'))						DEFINE("APP_HOME","/plex_web");
if (!defined('APP_PATH'))						DEFINE("APP_PATH", $_SERVER['DOCUMENT_ROOT'] . APP_HOME );

if (!defined('APP_NAME'))                       define('APP_NAME', 'Plex Media Import');
if (!defined('APP_ORGANIZATION'))               define('APP_ORGANIZATION', 'KLiK');
if (!defined('APP_OWNER'))                      define('APP_OWNER', 'bjorn');
if (!defined('APP_DESCRIPTION'))                define('APP_DESCRIPTION', 'Embeddable PHP Login System');



/****
*
* MySQL Database
*
*/
require_once("/home/bjorn/mysql_pwd.php");
DEFINE("__SQL_DB__", "pornhub_2");

if (!defined('DB_DATABASE'))                    define('DB_DATABASE', __SQL_DB__);
if (!defined('DB_HOST'))                        define('DB_HOST','127.0.0.1');
if (!defined('DB_USERNAME'))                    define('DB_USERNAME',__SQL_USER__);
if (!defined('DB_PASSWORD'))                    define('DB_PASSWORD' ,__SQL_PASSWD__);
if (!defined('DB_PORT'))                        define('DB_PORT' ,'');

if (!defined('Db_TABLE_PREFIX'))                   	define('Db_TABLE_PREFIX', "metatags" . "_" );
if (!defined('Db_TABLE_FILEDB'))                   	define('Db_TABLE_FILEDB', Db_TABLE_PREFIX."filedb");
if (!defined('Db_TABLE_STUDIO'))                   	define('Db_TABLE_STUDIO', Db_TABLE_PREFIX."studios");
if (!defined('Db_TABLE_ARTISTS'))                   define('Db_TABLE_ARTISTS', Db_TABLE_PREFIX."artists");


/******
*
* gmail connection
*
*/
DEFINE("__LOG_ERRORS__", 1);
DEFINE("__HTML_POPUP__", 1);
if (!defined('__SCRIPT_NAME__'))			    DEFINE("__SCRIPT_NAME__", "error");
if (!defined('__ERROR_LOG_DIR__'))			    DEFINE("__ERROR_LOG_DIR__", APP_PATH."/logs");
if (!defined('__ERROR_FILE_NAME__'))            DEFINE("__ERROR_FILE_NAME__", APP_NAME.".log");
if (!defined('ERROR_LOG_FILE'))                 DEFINE("ERROR_LOG_FILE", __ERROR_LOG_DIR__."/".__ERROR_FILE_NAME__);

if (!defined('__PHP_ASSETS_DIR__'))			    DEFINE("__PHP_ASSETS_DIR__", APP_PATH."/assets");
if (!defined('__PHP_INC_CORE_DIR__')) 			DEFINE("__PHP_INC_CORE_DIR__", __PHP_ASSETS_DIR__."/core");
if (!defined('__COMPOSER_LIB__')) 				DEFINE("__COMPOSER_LIB__", __PHP_ASSETS_DIR__."/lib/vendor");


if (!defined('__LAYOUT_PATH__'))				DEFINE("__LAYOUT_PATH__", __PHP_ASSETS_DIR__."/layouts/");

if (!defined('__LAYOUT_HEADER__'))				DEFINE("__LAYOUT_HEADER__", __LAYOUT_PATH__.'/header.php');
if (!defined('__LAYOUT_NAVBAR__'))				DEFINE("__LAYOUT_NAVBAR__", __LAYOUT_PATH__.'/navbar.php');
if (!defined('__LAYOUT_FOOTER__'))				DEFINE("__LAYOUT_FOOTER__", __LAYOUT_PATH__.'/footer.php');

if (!defined('__URL_PATH__'))				    DEFINE("__URL_PATH__",APP_HOME);
if (!defined('__URL_HOME__'))				    DEFINE("__URL_HOME__",$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].__URL_PATH__);
if (!defined('__LAYOUT_URL__'))					DEFINE("__LAYOUT_URL__", __URL_HOME__.'/assets/layouts/');

DEFINE('__THIS_PAGE__', str_replace(APP_HOME."/","",$_SERVER['SCRIPT_NAME']));


$navigation_link_array=array(

	"dropdown" => array(
		"View"=> array(
			"Artist" => "view/artist.php",
			"Studio" => "view/studio.php"

		),
		"Missing"=> array(
			"Titles" => "missing/title.php",
			"Artist" => "missing/artist.php",
			"Genre" => "missing/genre.php",
			"Studio" => "missing/studio.php"
		),
	),


"home" => array (
	"url" => "home.php",
	"text" => "home",
	"secure" => false,
	"js" => false
	),
	
"new" => array (
	"url" => "new.php",
	"text" => "New Videos",
	"secure" => false,
	"js" => false
	),
"test" => array (
	"url" => "test.php",
	"text" => "Test Page",
	"secure" => false,
	"js" => false
	),
	
"logout" => array (
	"url" => "#",
	"text" => "Log Out",
	"secure" => true,
	"js" => ' onclick="logout();"'
	)
	

);


$in_directory="Studio";
$lib_req="";
$lib_hidden="";
$lib_where="";

if(isset($_REQUEST['library']))
{
	$in_directory=$_REQUEST['library'];
	$lib_req="&library=$in_directory";
	$lib_where=" library = '".$in_directory."' AND ";
	$lib_hidden="<input type='hidden' value='".$in_directory."' name='library'>";
}
require_once(__PHP_ASSETS_DIR__."/header.inc.php");


?>
