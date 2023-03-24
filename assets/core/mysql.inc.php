<?php







function query_builder($fields = "select", $where = false, $group = false, $order = false, $limit = false, $offset = false)
{
    global $_SESSION;
    $conditional = false;
    $field_list = ' id, video_key,filename,thumbnail,title,artist,genre,studio,keyword,substudio,duration,favorite,added ,fullpath,library';

    if ($fields == "select") {
        $conditional = true;
        if ($limit != false || $offset != false) {
            $sql = ' select (@row_num:=@row_num +1) AS result_number, '.$field_list;
            $sql = $sql . ' from ( select ' . $field_list;
        //  $sql = ' select ' . $field_list;
        } else {
            $sql =  "select " . $field_list ;
        }
    } else {
        if (!str_contains($fields, "DISTINCT")) {
            $conditional = true;
        }
        $sql = "SELECT " . $fields;
    }

    $sql = $sql . ' from '.Db_TABLE_FILEDB.' ' ;



    if ($where == 'library') {
        $sql = $sql . " WHERE library = '". $_SESSION['library']."' ";
    } else {
        if ($conditional == true || $where != false) {
            $sql = $sql . " WHERE library = '". $_SESSION['library']."' ";
        }

        if ($where != false) {
            $sql = $sql . " AND  " . $where . "  ";
        }
    }
    if ($group != false) {
        $sql = $sql . " GROUP BY " . $group;
    }

    if ($order != false) {
        $sql = $sql . " ORDER BY " . $order;
    }

    if ($limit != false && $offset == false) {
        //   $sql = $sql . " LIMIT " . $limit.' ';


        $sql = $sql . " LIMIT " . $limit.' ) t1 ';
        $sql = $sql . ', (select @row_num:='.$offset.') t2';
    }

    if ($limit != false && $offset != false) {
   //     $sql = $sql . "  LIMIT " . $offset . ", " . $limit .'';


        $sql = $sql . "  LIMIT " . $offset . ", " . $limit .') t1,';
        $sql = $sql . ' (select @row_num:='.$offset.') t2';
    }

    // logger("SQL Builder", $sql);
    return $sql;
}
#class studios extends dbObject {
#    protected $dbTable = "home_vid";
#}
