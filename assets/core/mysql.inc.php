<?php 
    
require_once(__PHP_INC_CORE_DIR__.'/MysqliDb.inc.php');
require_once(__PHP_INC_CORE_DIR__.'/dbObject.inc.php');

/*
$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if (!$conn)
{
    die("Connection failed: ". mysqli_connect_error());
}
*/

$db = new MysqliDb ("localhost",__SQL_USER__,__SQL_PASSWD__,__SQL_DB__);
dbObject::autoload("models");


class MetaFiledb extends dbObject {
    protected $dbTable = Db_TABLE_FILEDB;
} 

class MetaStudio extends dbObject {
    protected $dbTable = Db_TABLE_STUDIO;
} 

class MetaArtist extends dbObject {
    protected $dbTable = Db_TABLE_ARTISTS;
} 


#class studios extends dbObject {
#    protected $dbTable = "home_vid";
#} 
