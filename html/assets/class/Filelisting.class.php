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
        // $this->db           = new PlexSql('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $this->db          = new PlexSql();
        $this->currentpage = $currentpage;
        $this->request     = $request;
        $this->urlPattern  = $urlPattern;
    }

    public function getSearchResults($field, $value)
    {
        $where   = "{$field}='{$value}'";

        $pageObj = new pageinate($where, $this->currentpage, $this->urlPattern);

        $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.'.$field, '%'.$value.'%', 'like');
        $results = $this->buildSQL([$pageObj->offset, $pageObj->itemsPerPage]);

        return [$results, $pageObj];
    }

    public function getVideoArray()
    {
        global $_SESSION;
        $tag_array = ['studio', 'substudio', 'genre', 'artist'];

        $where     = '';
        // . ' AND ';

        foreach ($tag_array as $tag) {
            if (isset($this->request[$tag]) && '' != $this->request[$tag]) {
                ${$tag}    = urldecode($this->request[$tag]);
                $uri[$tag] = ${$tag};
                if ('studio' == $tag || 'substudio' == $tag) {
                    $studio_key = $tag;
                }
            }
        }

        if (!isset($studio_key)) {
            $studio_key = 'studio';
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

        $pageObj   = new pageinate($where, $this->currentpage, $this->urlPattern);

        foreach ($tag_array as $tag) {
            if (isset($this->request[$tag]) && '' != $this->request[$tag]) {
                $value = '%'.$this->request[$tag].'%';
                $comp  = ' like';

                if ('NULL' == $this->request[$tag]) {
                    $value = null;
                    $comp  = ' IS';
                }
                // dump(['m.'.$tag, $value, $comp]);

                $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.'.$tag, $value, $comp);
            }
        }

        if (isset($this->request['sort'], $this->request['direction'])) {
            $this->db->orderBy($this->request['sort'], $this->request['direction']);
        }

        $results   = $this->buildSQL([$pageObj->offset, $pageObj->itemsPerPage]);

        return [$results, $pageObj, $uri];
    }

    public function getLibrary()
    {
        global $_SESSION;
        if ('All' != $_SESSION['library']) {
            return " AND m.library = '".$_SESSION['library']."'  ";
        }

        return null;
    }

    public function getVideoDetails($id)
    {
        $sql = 'SELECT ';
        $sql .= 'COALESCE (c.title,m.title) as title, ';
        $sql .= 'COALESCE (c.artist,m.artist) as artist, ';
        $sql .= 'COALESCE (c.genre,m.genre) as genre, ';
        $sql .= 'COALESCE (c.studio,m.studio) as studio, ';
        $sql .= 'COALESCE (c.substudio,m.substudio) as substudio, ';
        $sql .= 'COALESCE (c.keyword,m.keyword) as keyword, ';
        if (null === $this->getLibrary()) {
            $sql .= 'm.library as library, ';
        }
        $sql .= 'i.format, i.bit_rate, i.width, i.height, ';
        $sql .= 'f.filename, f.thumbnail, f.fullpath, f.duration, ';
        $sql .= 'f.filesize, f.added, f.id, f.video_key FROM metatags_video_file f ';
        $sql .= 'INNER JOIN metatags_video_metadata m on f.video_key=m.video_key '.$this->getLibrary();
        $sql .= 'LEFT OUTER JOIN metatags_video_info i on f.video_key=i.video_key LEFT JOIN metatags_video_custom c on m.video_key=c.video_key ';
        $sql .= "WHERE   f.id = '".$id."'";

        return $this->db->query($sql);
    }

    private function loopTags($array) {}

    private function buildSQL($limit = null)
    {
        // SELECT @rownum := @rownum + 1 AS rownum, T1.* FROM ( ) AS T1, (SELECT @rownum := 0) AS r

        //$fieldArray = ['m.title', 'm.artist', 'm.genre', 'm.studio', 'm.substudio', 'm.keyword'];
        //$fieldArray = ['m.title', 'm.artist', 'm.genre', 'm.studio', 'm.substudio', 'm.keyword'];

        $fieldArray = [ 'COALESCE (c.title,m.title) as title ',
         'COALESCE (c.artist,m.artist) as artist ',
         'COALESCE (c.genre,m.genre) as genre ',
         'COALESCE (c.studio,m.studio) as studio ',
         'COALESCE (c.substudio,m.substudio) as substudio ',
         'COALESCE (c.keyword,m.keyword) as keyword '];

        $this->db->join(Db_TABLE_VIDEO_TAGS.' m', 'f.video_key=m.video_key', 'INNER');
        $this->db->join(Db_TABLE_VIDEO_INFO.' i', 'f.video_key=i.video_key', 'LEFT OUTER');

        $this->db->join(Db_TABLE_VIDEO_CUSTOM.' c', 'f.video_key=c.video_key', 'LEFT');
        if (null !== $this->getLibrary()) {
            $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.library', $_SESSION['library']);
           
        }
        // $fieldArray[] = 'm.library';

        $fieldArray = array_merge($fieldArray, [
            'i.format', 'i.bit_rate', 'i.width', 'i.height', 'f.library',
            'f.filename', 'f.thumbnail', 'f.fullpath', 'f.duration', 'f.filesize', 'f.added', 'f.id', 'f.video_key']);

        $joinQuery  = $this->db->getQuery(
            Db_TABLE_VIDEO_FILE.' f',
            $limit,
            $fieldArray
        );
//dd($joinQuery);
        $query      = 'SELECT @rownum := @rownum + 1 AS rownum, T1.* FROM ( '.$joinQuery.' ) AS T1, (SELECT @rownum := '.$limit[0].') AS r';
        $results    = $this->db->rawQuery($query);

        // dd($this->db->getlastquery());
        return $results;
    }
}
