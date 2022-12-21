<?php





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

class MetaFileinfo extends dbObject
{
    protected $dbTable = Db_TABLE_FILEINFO;
}


function query_builder($fields = "select", $where = FALSE, $group = FALSE, $order = FALSE, $limit = FALSE, $offset = FALSE)
{
    $field_list = ' id, video_key,filename,thumbnail,title,artist,genre,studio,substudio,duration,favorite,fullpath,library ';

    if($fields == "select") {  

    $sql = ' select (@row_num:=@row_num +1) AS result_number, '.$field_list;
    $sql = $sql . ' from ( select ' . $field_list;
    $sql = $sql . ' from '.Db_TABLE_FILEDB.' ';
    
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
        $sql = $sql . " LIMIT " . $limit.' ) t1,';
        $sql = $sql . ' (select @row_num:='.$offset.') t2';

    }
    if($limit != FALSE && $offset != FALSE) {
        $sql = $sql . "  LIMIT " . $offset . ", " . $limit .') t1,';
        $sql = $sql . ' (select @row_num:='.$offset.') t2';
    }

    //logger("SQL Builder", $sql);
    return $sql;

}
#class studios extends dbObject {
#    protected $dbTable = "home_vid";
#} 
