<?php

require_once __PHP_ASSETS_DIR__.'/defines.php';

set_include_path(get_include_path() . PATH_SEPARATOR . __COMPOSER_LIB__);
require_once __COMPOSER_LIB__.'/autoload.php';

if (!defined('__ERROR_LOG_DIR__')) DEFINE("__ERROR_LOG_DIR__", APP_PATH."/logs");


$logfile_name="plexweb.log";
if (__HTML_POPUP__ == true) $logfile_name="plexweb.html.log";

if (!defined('__ERROR_FILE_NAME__')) DEFINE("__ERROR_FILE_NAME__", $logfile_name);
if (!defined('ERROR_LOG_FILE')) DEFINE("ERROR_LOG_FILE", __ERROR_LOG_DIR__."/".__ERROR_FILE_NAME__);



$all = opendir(__PHP_INC_CORE_DIR__);
while ($file = readdir($all)) {
    if (!is_dir(__PHP_INC_CORE_DIR__.'/'.$file) ) {
        if(preg_match("/\.(php)$/",$file)){
			$f = fopen(__PHP_INC_CORE_DIR__.'/'.$file, 'r');
			$line = fgets($f);
			fclose($f);
			if (strpos($line,'#skip') == false) {
				require_once(__PHP_INC_CORE_DIR__.'/'.$file);
			} 
		}
    }
}


$colors = new Colors();


if (__SCRIPT_NAME__ != "logs" ) 
{
	if (is_file(ERROR_LOG_FILE)){
		$lines = count(file(ERROR_LOG_FILE));
		if($lines > 1000){
			unlink(ERROR_LOG_FILE);
		}
	}
}


if (__SCRIPT_NAME__ != "logs" ) logger("start for " . __SCRIPT_NAME__);

if(!isset($_SESSION['library']))$_SESSION['library']="Studio"; 
if(isset($_REQUEST['library']))$_SESSION['library']=$_REQUEST['library'];
$in_directory=$_SESSION['library'];

logger("in_directory " , $in_directory);
logger("_SESSION _SESSION " , $_SESSION);


//$lib_req="&library=$in_directory";
$lib_where=" library = '".$in_directory."' AND ";
$lib_hidden="<input type='hidden' value='".$in_directory."' name='library'>";


if(!isset($_SESSION['direction'])) $_SESSION['direction']="ASC"; 
if(isset($_REQUEST['direction']))
{
	if($_REQUEST['direction'] == 'ASC')
	{
      $_SESSION['direction'] = 'DESC';
	}
}

if(!isset($_SESSION['sort']))$_SESSION['sort']="title"; 
if(isset($_REQUEST['sort'])) $_SESSION['sort'] = $_REQUEST['sort'];


?>