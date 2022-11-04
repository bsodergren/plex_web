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


function query_builder($fields="select",$where=false,$group=false,$order=false,$limit=false)
{
    
    if($fields == "select" )
    {
        $sql = "SELECT id,filename,title,artist,genre,studio,substudio,fullpath,favorite,thumbnail from ".Db_TABLE_FILEDB;  
    } else {
        
        $sql = "SELECT ".$fields." from ".Db_TABLE_FILEDB;  
    }
    
    if($where != false )
    {
        $sql = $sql . " WHERE " . $where;
        
    }
    
    if($group != false )
    {
        $sql = $sql . " GROUP BY " . $group;
        
    }
    
    if($order != false )
    {
        $sql = $sql . " ORDER BY " . $order;
        
    }
    logger("SQL Builder", $sql);
    return $sql;
    
}
#class studios extends dbObject {
#    protected $dbTable = "home_vid";
#} 
