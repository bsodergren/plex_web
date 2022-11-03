<?php

set_include_path(get_include_path() . PATH_SEPARATOR . __COMPOSER_LIB__);
require_once __COMPOSER_LIB__.'/autoload.php';

require_once __PHP_ASSETS_DIR__.'/defines.php';

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
logger("start for " . __SCRIPT_NAME__);


?>