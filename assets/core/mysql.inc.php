<?php

require_once(__PHP_INC_CORE_DIR__ . '/MysqliDb.inc.php');
require_once(__PHP_INC_CORE_DIR__ . '/dbObject.inc.php');

/*
$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if (!$conn)
{
    die("Connection failed: ". mysqli_connect_error());
}
*/

$db = new MysqliDb ("localhost", __SQL_USER__, __SQL_PASSWD__, __SQL_DB__);
dbObject::autoload("models");


class MetaSettings extends dbObject
{
    protected $dbTable = Db_TABLE_SETTINGS;
}

class MetaFiledb extends dbObject
{
    protected $dbTable = Db_TABLE_FILEDB;
}

class MetaStudio extends dbObject
{
    protected $dbTable = Db_TABLE_STUDIO;
}

class MetaArtist extends dbObject
{
    protected $dbTable = Db_TABLE_ARTISTS;
}


function query_builder($fields = "select", $where = FALSE, $group = FALSE, $order = FALSE, $limit = FALSE, $offset = FALSE)
{

    if($fields == "select") {
        $sql = "SELECT id,filename,thumbnail,title,artist,genre,studio,substudio,duration,favorite,fullpath from " . Db_TABLE_FILEDB;
    } else {

        $sql = "SELECT " . $fields . " from " . Db_TABLE_FILEDB;
    }

    if($where != FALSE) {
        $sql = $sql . " WHERE " . $where;

    }

    if($group != FALSE) {
        $sql = $sql . " GROUP BY " . $group;

    }

    if($order != FALSE) {
        $sql = $sql . " ORDER BY " . $order;

    }

    if($limit != FALSE && $offset == FALSE) {
        $sql = $sql . " LIMIT " . $limit;

    }
    if($limit != FALSE && $offset != FALSE) {
        $sql = $sql . "  LIMIT " . $offset . ", " . $limit;

    }

    //logger("SQL Builder", $sql);
    return $sql;

}
#class studios extends dbObject {
#    protected $dbTable = "home_vid";
#} 
