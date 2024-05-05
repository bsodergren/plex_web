<?php

namespace Plex\Modules\Database;

/*
 * plex web viewer
 */

use Plex\Core\Request;

class PlexSql extends \MysqliDb
{
    public $limit = false;
    public $db;

    public $offset = false;
    public $where = '';
    public $groupBy = '';
    public $orderBy = '';
    public $_tableName;
    public static $DB;

    public function __construct()
    {
        $db = parent::getInstance();
        if (null === $db) {
            parent::__construct('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);
            $db = parent::getInstance();
        }
        $this->db = $db;

        self::$DB = $db;
        // return $this->db;
    }

    public static function getLastest($field, $days = 1)
    {
        return $field.' > Today() - interval '.$days.' day ';
    }

    public static function getAlphaKey($field, $key)
    {
        $url_array = Request::$url_array;
        if (null === $field) {
            $field = $url_array['sortDefault'];
        }
        $tag_string = implode(',', Request::$tag_types);
        $f = explode('.', $field);
        if (str_contains($tag_string, $f[1])) {
            if ('All' == $key) {
                return null;
            }
            if ('#' == $key) {
                return $field." REGEXP '^[0-9]'";
            }
            if ('None' == $key) {
                return $field.' IS NULL';
            }
            if ('artist' == $f[1]) {
                return '('.$field." LIKE '".$key."%' OR ".$field." LIKE '%,".$key."%')";
            }

            return $field." LIKE '".$key."%' ";
        }

        return null;
    }

    public static function getFilterList($field)
    {
        $tag_array = ['studio', 'substudio', 'genre', 'artist'];
        $query = [];
        foreach ($tag_array as $tag) {
            if ($tag == $field) {
                continue;
            }
            if (isset($_REQUEST[$tag])) {
                $query[] = $tag.' LIKE "%'.$_REQUEST[$tag].'%" ';
            }
        }

        if ('All' != $_SESSION['library']) {
            $query[] = " library = '".$_SESSION['library']."'  ";
        }
        if (\count($query) > 0) {
            $querySQl = ' WHERE ';
            $partsSQL = implode(' AND ', $query);
            $querySQl .= $partsSQL;
        }

        $sql = 'SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX('.$field.", ',', n.digit+1), ',', -1) val FROM ".Db_TABLE_VIDEO_METADATA.' INNER JOIN (SELECT 0 digit UNION ALL SELECT
    1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6) n
    ON LENGTH(REPLACE('.$field.", ',' , '')) <= LENGTH(".$field.')-n.digit  '.$querySQl.'  ORDER BY `val` ASC';

        $res = self::$DB->query($sql);
        foreach ($res as $k => $g) {
            $array[] = $g['val'];
        }

        return $array;
    }

    public function getDuplicates($column)
    {
        global $_SESSION;

        $library = '';
        if ('All' != $_SESSION['library']) {
            $library = " WHERE library = '".$_SESSION['library']."' ";
        }
        $query = 'SELECT '.$column.',count('.$column.') FROM `'.Db_TABLE_VIDEO_FILE.'` '.$library.' group
        by '.$column.' having COUNT('.$column.') > 1;';

        // echo $query;
        return $this->db->query($query);
    }

    // public function showDupes($column,$value)
    // {

    //     $library = '';
    //     if ('All' != $_SESSION['library']) {
    //         $library = "  AND library = '".$_SESSION['library']."' ";
    //     }

    //     $query = PlexSql::query_builder(''.Db_TABLE_VIDEO_FILE.'','select', "`v.".$column."` = '".$value."' ");
    //     //$query = "SELECT * FROM `'.Db_TABLE_VIDEO_FILE.'` WHERE `".$column."` = '".$value."' ".  $library;
    //     utmdd($query);
    //     return $db->query($query);
    // }
    public static function getLibrary($prefix = 'm.', $useAnd = 'AND')
    {
        global $_SESSION;
        if ('All' != $_SESSION['library']) {
            return ' '.$useAnd.' '.$prefix."library = '".$_SESSION['library']."'  ";
        }

        return null;
    }

    public function showDupes($column, $value)
    {
        // SELECT @rownum := @rownum + 1 AS rownum, T1.* FROM ( ) AS T1, (SELECT @rownum := 0) AS r

        $fieldArray = ['m.title', 'm.artist', 'm.genre', 'm.studio', 'm.substudio', 'm.keyword'];
        // $this->db->joinWhere(Db_TABLE_VIDEO_FILE.' v', 'v.'.$column, $value);
        $this->db->where('v.'.$column, $value);
        $this->db->join(Db_TABLE_VIDEO_METADATA.' m', 'v.video_key=m.video_key', 'INNER');
        $this->db->join(Db_TABLE_VIDEO_INFO.' i', 'v.video_key=i.video_key', 'LEFT OUTER');
        if (null !== self::getLibrary()) {
            $this->db->joinWhere(Db_TABLE_VIDEO_METADATA.' m', 'm.library', $_SESSION['library']);
        }
        //        $this->db->where('v.'.$column, $value);
        // $fieldArray[] = 'm.library';

        $fieldArray = array_merge($fieldArray, [
            'i.format', 'i.bit_rate', 'i.width', 'i.height', 'v.library', 'v.preview',
            'v.filename', 'v.thumbnail', 'v.fullpath', 'v.duration', 'v.filesize', 'v.added', 'v.id', 'v.video_key']);

        $joinQuery = $this->db->getQuery(
            Db_TABLE_VIDEO_FILE.' v',
            null,
            $fieldArray
        );

        // $query      = 'SELECT @rownum := @rownum + 1 AS rownum, T1.* FROM ( '.$joinQuery.' ) AS T1, (SELECT @rownum := '.$limit[0].') AS r';
        $results = $this->db->rawQuery($joinQuery);

        // return $this->db->getlastquery();
        return $results;
    }

    public function getArtistsList()
    {
        $res = $this->getArtists();
        foreach ($res as $k => $v) {
            $array[] = $v['artist'];
        }
        $namesStr = implode(',', $array);
        $namesArray = explode(',', $namesStr);
        $namesArray = array_unique($namesArray);
        sort($namesArray);
        $namesStr = implode(',', $namesArray);
        $namesStr = '"'.implode('","', $namesArray).'"';

        return trim(str_replace('" ', '"', $namesStr));
    }

    public function getArtists()
    {
        $sql = 'SELECT ';

        $sql .= 'COALESCE (c.artist,m.artist) as artist, ';
        $sql .= 'v.video_key FROM '.Db_TABLE_VIDEO_FILE.' v ';
        $sql .= 'INNER JOIN '.Db_TABLE_VIDEO_METADATA.' m on v.video_key=m.video_key '.self::getLibrary();
        $sql .= 'LEFT JOIN '.Db_TABLE_VIDEO_CUSTOM.' c on m.video_key=c.video_key ';

        $where = ''; // $this->pwhere("(artist is not null and artist != 'Missing')");

        return $this->db->query($sql.$where);
    }

    public function getQuery($tableName, $numRows = null, $columns = '*')
    {
        if (empty($columns)) {
            $columns = '*';
        }

        $column = \is_array($columns) ? implode(', ', $columns) : $columns;

        if (!str_contains($tableName, '.')) {
            $this->_tableName = self::$prefix.$tableName;
        } else {
            $this->_tableName = $tableName;
        }

        $this->_query = 'SELECT '.implode(' ', $this->_queryOptions).' '.
            $column.' FROM '.$this->_tableName;

        $stmt = $this->_buildQuery($numRows);

        if ($this->isSubQuery) {
            return $this;
        }

        return $this->_lastQuery;
    }
    // public $fieldList ='id, video_key,thumbnail,title,artist,genre,studio,keyword,substudio,duration,favorite,added,filename ,fullpath,library,filesize';

    //  SELECT
    // m.video_key,thumbnail,m.title,m.artist,m.genre,m.studio,m.keyword,m.substudio,v.filename ,v.fullpath,m.library,v.filesize
    // FROM '.Db_TABLE_VIDEO_FILE.' v INNER JOIN '.Db_TABLE_VIDEO_METADATA.' m on m.video_key=v.video_key
    // AND m.studio = 'Brazzers' AND m.library = 'Pornhub' AND m.genre like '%MMF%' ORDER BY m.title ASC LIMIT 0, 5
    public function pselect($table, $fields = 'select')
    {
        //  $this->sql_table = $table;
        //        $field_list      = ' id, video_key,thumbnail,title,artist,genre,studio,keyword,substudio,duration,favorite,added,filename ,fullpath,library,filesize';
        $field_list = 'v.id, v.video_key,v.preview,v.thumbnail,m.title,m.artist,m.genre,m.studio,m.keyword,m.substudio,v.added,v.filename ,v.fullpath,m.library,v.filesize';
        if ('select' == $fields) {
            $sql = 'select '.$field_list;
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
            $sql = $sql.'  LIMIT '.$this->offset.', '.$this->limit.'';

            // $sql = $sql.'  LIMIT '.$this->offset.', '.$this->limit.') t1,';
            // $sql = $sql.' (select @row_num:='.$this->offset.') t2';
        }

        return $sql;
    }

    public function pwhere($where)
    {
        global $_SESSION;

        $library = '';
        if ('All' != $_SESSION['library']) {
            $library = " library = '".$_SESSION['library']."' ";
        }

        if ('library' != $where) {
            if ('' != $library) {
                $library = ' AND '.$library;
            }
            $sql = ' WHERE '.$where.$library;
        } else {
            if ('' != $library) {
                $library = ' WHERE '.$library;
            }

            $sql = $library;
        }

        $this->where = $sql;

        return $sql;
    }

    public function pgroupBy($fields)
    {
        $this->groupBy = ' GROUP BY '.$fields;
    }

    public function porderBy($fields)
    {
        $this->orderBy = ' ORDER BY '.$fields;
    }

    public function psetLimit($limit)
    {
        $this->limit = $limit;
    }

    public function psetOffset($offset)
    {
        $this->offset = $offset;
    }

    public static function query_builder($table, $fields = 'select', $where = false, $group = false, $order = false, $limit = false, $offset = false)
    {
        $query = new self();
        if (false != $where) {
            $query->pwhere($where);
        }
        if (false != $group) {
            $query->pgroupBy($group);
        }
        if (false != $order) {
            $query->porderBy($order);
        }
        if (false != $limit) {
            $query->psetLimit($limit);
        }
        if (false != $offset) {
            $query->psetOffset($offset);
        }

        $sql = $query->pselect($table, $fields);
        logger('SQL Builder', $sql);

        return $sql;
    }
}
