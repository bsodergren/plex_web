<?php

require_once __PHP_ASSETS_DIR__.'/defines.php';

if (__SCRIPT_NAME__ != "logs" ) 
{
	if (is_file(ERROR_LOG_FILE)){
		$lines = count(file(ERROR_LOG_FILE));
		if($lines > 1000){
			unlink(ERROR_LOG_FILE);
		}
	}
}

set_include_path(get_include_path() . PATH_SEPARATOR . __COMPOSER_LIB__);
require_once __COMPOSER_LIB__.'/autoload.php';


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

if (__SCRIPT_NAME__ != "logs" ) logger("start for " . __SCRIPT_NAME__);

?>