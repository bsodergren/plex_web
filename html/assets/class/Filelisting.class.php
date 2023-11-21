<?php
/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */
class FileListing
{
    public object $db;
    public $currentpage;
    public $request;
    public $urlPattern;

    public function __construct($request = '', $currentpage = '', $urlPattern = '')
    {
        $this->db           = new MysqliDb('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $this->currentpage  = $currentpage;
        $this->request      = $request;
        $this->urlPattern   = $urlPattern;
    }

    public function getSearchResults($field, $value)
    {

       $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.'.$field, '%'.$value.'%', 'like');
        $results =  $this->buildSQL();
        return $results;
    }
    
    public function getVideoArray()
    {
        global $_SESSION;
        $where   = '';
        // . ' AND ';
        if (isset($this->request['substudio'])) {
            // if  (!isset($_REQUEST['allfiles']))
            // {
            $substudio        = urldecode($this->request['substudio']);
            $uri['substudio'] = [
                $this->request['substudio'],
                $substudio,
            ];
            $studio_key       = 'substudio';
            // }
            // $studio_key="substudio";
        }

        if (isset($this->request['studio'])) {
            $studio        = urldecode($this->request['studio']);
            // $studio = str_replace("_","/",$studio);
            $uri['studio'] = [
                $studio,
            ];
            if (!isset($studio_key)) {
                $studio_key = 'studio';
            }
        }

        if (isset($this->request['genre'])) {
            $genre        = urldecode($this->request['genre']);
            $uri['genre'] = [
                $genre,
            ];
        }

        if (isset($_SESSION['sort'])) {
            $uri['sort']           = $_SESSION['sort'];
            $this->request['sort'] = $_SESSION['sort'];
        }

        if (isset($_SESSION['direction'])) {
            $uri['direction']           = $_SESSION['direction'];
            $this->request['direction'] = $_SESSION['direction'];
        }
        if (isset($uri)) {
            $sql_studio = '';
            $res_array  = uri_SQLQuery($uri);

            if (array_key_exists('sort', $res_array)) {
                $order_sort = $res_array['sort'];
            }

            if (array_key_exists('sql', $res_array)) {
                $sql_studio = $res_array['sql'];
            }

            if (isset($this->request['genre'])) {
                $where = str_replace("genre  = '".$this->request['genre']."'", 'genre like \'%'.$this->request['genre'].'%\'', $sql_studio);
            }
            if (!isset($this->request['allfiles']) && '' != $sql_studio) {
                $where = str_replace("studio = 'null'", 'studio IS NULL', $sql_studio);
            } else {
                $studio_key      = '';
                $uri['allfiles'] = $this->request['allfiles'];
                $where           = $sql_studio;
                $genre           = '';
            }
        }
        $pageObj = new pageinate($where, $this->currentpage, $this->urlPattern);

        if (isset($this->request['studio'])) {
            $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.studio', '%'.$this->request['studio'].'%', 'like');
        }
        if (isset($this->request['genre'])) {
            $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.genre', '%'.$this->request['genre'].'%', 'like');
        }
        if (isset($this->request['sort'], $this->request['direction'])) {
            $this->db->orderBy($this->request['sort'], $this->request['direction']);
        }

        $results =  $this->buildSQL([$pageObj->offset, $pageObj->itemsPerPage]);

        return [$results, $pageObj, $uri];
    }

    public function getVideoDetails($id)
    {
        $sql = "SELECT  COALESCE (c.title,m.title) as title, ";
        $sql .= "COALESCE (c.artist,m.artist) as artist, ";
        $sql .= " COALESCE (c.genre,m.genre) as genre, ";
        $sql .= "COALESCE (c.studio,m.studio) as studio, ";
        $sql .= "COALESCE (c.substudio,m.substudio) as substudio, ";
        $sql .= "COALESCE (c.keyword,m.keyword) as keyword, ";
        $sql .= "i.format, i.bit_rate, i.width, i.height, ";
        $sql .= "f.filename, f.thumbnail, f.fullpath, f.duration, ";
        $sql .= "f.filesize, f.added, f.id, f.video_key FROM metatags_video_file f ";
        $sql .= "INNER JOIN metatags_video_metadata m on f.video_key=m.video_key AND m.library = '". $_SESSION['library'] ."'  ";
        $sql .= "LEFT OUTER JOIN metatags_video_info i on f.video_key=i.video_key LEFT JOIN metatags_video_custom c on m.video_key=c.video_key WHERE   f.id = '".$id."'";

        return $this->db->query($sql);
    }

    private function buildSQL($limit = null)
    {
        $this->db->join(Db_TABLE_VIDEO_TAGS.' m', 'f.video_key=m.video_key', 'INNER');
        $this->db->join(Db_TABLE_VIDEO_INFO.' i', 'f.video_key=i.video_key', 'LEFT OUTER');
        $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.library', $_SESSION['library']);

        $results = $this->db->get(
            Db_TABLE_VIDEO_FILE.' f',
            $limit,
            [
                'm.title', 'm.artist', 'm.genre', 'm.studio', 'm.substudio', 'm.keyword',

                'i.format', 'i.bit_rate', 'i.width', 'i.height',
                'f.filename', 'f.thumbnail', 'f.fullpath', 'f.duration', 'f.filesize', 'f.added', 'f.id', 'f.video_key']
        );

        return $results;
    }
}
