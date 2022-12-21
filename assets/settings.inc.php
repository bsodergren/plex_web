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
      
        define($u->name ,$u->value);

    }
    define("__SETTINGS__", $setting);
}

?>