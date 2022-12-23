<?php 

require_once(__PHP_INC_CORE_DIR__ . '/MysqliDb.inc.php');
require_once(__PHP_INC_CORE_DIR__ . '/dbObject.inc.php');


$db = new MysqliDb ("localhost", __SQL_USER__, __SQL_PASSWD__, __SQL_DB__);
dbObject::autoload("models");

class MetaSettings extends dbObject
{
    protected $dbTable = Db_TABLE_SETTINGS;
}

$settings = new MetaSettings ();
$val = $settings->orderBy("type")->get();
if ($val) {

    foreach ($val as $u) {
        $setting[$u->name]=  $u->type.";".$u->value;

        if ($u->type = "array") {
            define($u->name, json_decode($u->value,1) );


            if(defined('__DISPLAY_PAGES__') && key_exists(__THIS_FILE__,__DISPLAY_PAGES__) ) 
            {
                define('__SHOW_PAGES__', __DISPLAY_PAGES__[__THIS_FILE__]['pages']);
                define('__SHOW_SORT__', __DISPLAY_PAGES__[__THIS_FILE__]['sort']);

                if ( __SHOW_PAGES__ == 0 && __SHOW_SORT__ == 0 ){
                    define('__BOTTOM_NAV__', 0);
                } else {
                    define('__BOTTOM_NAV__', 1);
                }
            }

        } else {
            define($u->name, $u->value);
        }
    }
    define("__SETTINGS__", $setting);
}

if (!defined('__BOTTOM_NAV__')) {
    define('__BOTTOM_NAV__', 0);
}


?>