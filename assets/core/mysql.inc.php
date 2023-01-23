<?php







function query_builder($fields = "select", $where = FALSE, $group = FALSE, $order = FALSE, $limit = FALSE, $offset = FALSE)
{

    global $_SESSION;
    $conditional = false;
    $field_list = ' id, video_key,filename,thumbnail,title,artist,genre,studio,substudio,duration,favorite,added ,fullpath,library';

    if($fields == "select") {
        $conditional = true;
        if($limit != FALSE || $offset != FALSE)
        {
        
            $sql = ' select (@row_num:=@row_num +1) AS result_number, '.$field_list;
            $sql = $sql . ' from ( select ' . $field_list;

        } else {
            $sql =  "select " . $field_list ;
        }

    } else {
        if(!str_contains($fields,"DISTINCT")){
            $conditional = true;
        }
        $sql = "SELECT " . $fields;
    }

    $sql = $sql . ' from '.Db_TABLE_FILEDB.' ' ;
    
    if ( $conditional == true || $where != FALSE )
    {
        $sql = $sql . " WHERE library = '". $_SESSION['library']."' ";
    }


    if($where != FALSE) {
        $sql = $sql . " AND  " . $where . "  ";
        
    }

    if($group != FALSE) {
        $sql = $sql . " GROUP BY " . $group;

    }

    if($order != FALSE) {
        $sql = $sql . " ORDER BY " . $order;

    }

    if($limit != FALSE && $offset == FALSE) {
        $sql = $sql . " LIMIT " . $limit.' ) t1 ';
        $sql = $sql . ', (select @row_num:='.$offset.') t2';

    }
    if($limit != FALSE && $offset != FALSE) {
        $sql = $sql . "  LIMIT " . $offset . ", " . $limit .') t1,';
        $sql = $sql . ' (select @row_num:='.$offset.') t2';
    }

   // logger("SQL Builder", $sql);
    return $sql;

}
#class studios extends dbObject {
#    protected $dbTable = "home_vid";
#} 
