<?php
/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */
class PlexSql
{
    public $limit   = false;
    public $offset  = false;
    public $where   = '';
    public $groupBy = '';
    public $orderBy = '';

    // public $fieldList ='id, video_key,thumbnail,title,artist,genre,studio,keyword,substudio,duration,favorite,added,filename ,fullpath,library,filesize';

  //  SELECT 
    // m.video_key,thumbnail,m.title,m.artist,m.genre,m.studio,m.keyword,m.substudio,f.filename ,f.fullpath,m.library,f.filesize 
    // FROM metatags_video_file f INNER JOIN metatags_video_metadata m on m.video_key=f.video_key 
    // AND m.studio = 'Brazzers' AND m.library = 'Pornhub' AND m.genre like '%MMF%' ORDER BY m.title ASC LIMIT 0, 5
    public function select($table, $fields = 'select')
    {
      //  $this->sql_table = $table;
//        $field_list      = ' id, video_key,thumbnail,title,artist,genre,studio,keyword,substudio,duration,favorite,added,filename ,fullpath,library,filesize';
        $field_list      = 'f.id, f.video_key,f.thumbnail,m.title,m.artist,m.genre,m.studio,m.keyword,m.substudio,f.added,f.filename ,f.fullpath,m.library,f.filesize';
        if ('select' == $fields) {
                $sql =  'select '.$field_list;
            
        } else {
            if (!str_contains($fields, 'DISTINCT')) {
                $conditional = true;
            }
            $sql = 'SELECT '.$fields;
        }

        $sql = $sql.' FROM '.$table.' ';
        $sql .= $this->where;
        $sql .= $this->groupBy;
        $sql .= $this->orderBy;

        if (false != $this->limit && false == $this->offset) {
            $sql = $sql.' LIMIT '.$this->limit;
        }

        if (false != $this->limit && false != $this->offset) {
            $sql = $sql . "  LIMIT " . $this->offset . ", " . $this->limit .'';

            // $sql = $sql.'  LIMIT '.$this->offset.', '.$this->limit.') t1,';
            // $sql = $sql.' (select @row_num:='.$this->offset.') t2';
        }

       
        return $sql;
    }

    public function where($where)
    {
        global $_SESSION;
        if ('library' == $where) {
            $sql = " WHERE library = '".$_SESSION['library']."' ";
        } else {
            $sql = " WHERE library = '".$_SESSION['library']."' AND  ".$where;
        }

        $this->where = $sql;
    }

    public function groupBy($fields)
    {
        $this->groupBy =  ' GROUP BY '.$fields;
    }

    public function orderBy($fields)
    {
        $this->orderBy =  ' ORDER BY '.$fields;
    }

    public function setLimit($limit)
    {
        $this->limit  = $limit;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }
}

function query_builder($table, $fields = 'select', $where = false, $group = false, $order = false, $limit = false, $offset = false)
{
    $query = new PlexSql();
    if (false != $where) {
        $query->where($where);
    }
    if (false != $group) {
        $query->groupBy($group);
    }
    if (false != $order) {
        $query->orderBy($order);
    }
    if (false != $limit) {
        $query->setLimit($limit);
    }
    if (false != $offset) {
        $query->setOffset($offset);
    }

    $sql = $query->select($table, $fields);
    logger('SQL Builder', $sql);

    return $sql;
}
