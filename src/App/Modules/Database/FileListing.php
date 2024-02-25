<?php

namespace Plex\Modules\Database;

use Plex\Core\Request;
use Plex\Modules\Database\PlexSql;
use Plex\Template\Pageinate\Pageinate;

class FileListing
{
    public object $db;
    public $currentpage;
    public $request;
    public $urlPattern;
    public static $searchId;
    public object $ReqObj;

    public function __construct(Request $ReqObj)
    {
        $this->ReqObj = $ReqObj;
        $uri = $this->ReqObj->getURI();
        $urlPattern = $this->ReqObj->geturlPattern();
        $url_array = $this->ReqObj->url_array();
        $currentpage = $this->ReqObj->currentPage;
        // $this->db           = new PlexSql('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $this->db = new PlexSql();
        $this->currentpage = $currentpage;
        $this->request = $this->ReqObj->http_request;
        $this->urlPattern = $urlPattern;
    }

    public function saveSearch($query)
    {
        $db = new PlexSql();
        $res = $db->rawQuery($query);
        if (\count($res) > 0) {
            foreach ($res as $k => $row) {
                $vids[] = $row['id'];
            }

            $vidsStr = implode(',', $vids);

            $db->where('video_list', $vidsStr);
            $user = $db->getOne(Db_TABLE_SEARCH_DATA);
            if (null !== $user['id']) {
                self::$searchId = $user['id'];

                return $user['id'];
            }
            $data = [
                'video_list' => $vidsStr,
                // "updatedAt" => $db->now()
            ];

            $id = $db->insert(Db_TABLE_SEARCH_DATA, $data);
            //   dump("new index " . $id);
            self::$searchId = $id;

            return $id;
        }

        return 0;
    }

    public function getSearchResults($field, $query)
    {
        if(!is_array($query))
        {
            $query = [$query];
        }
        
        dump([$field,$query]);
        foreach($query as $search){
            $WhereList[] = "{$field} like '%{$search}%'";
        }

        $where = implode(" and ",$WhereList);
        dump($where);

        $pageObj = new Pageinate($where, $this->currentpage, $this->urlPattern);

      //  $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.'.$field, '%'.$value.'%', 'like');
      foreach($query as $search){
        $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.'.$field, '%'.$search.'%', 'like');
    }
        $results = $this->buildSQL([$pageObj->offset, $pageObj->itemsPerPage]);

        return [$results, $pageObj];
    }

    public function getVideoArray()
    {
        global $_SESSION;
        $tag_array = ['studio', 'substudio', 'genre', 'artist'];

        $where = '';
        // . ' AND ';

        foreach ($tag_array as $tag) {
            if (isset($this->request[$tag]) && '' != $this->request[$tag]) {
                ${$tag} = urldecode($this->request[$tag]);
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
            $uri['sort'] = $_SESSION['sort'];
            $this->request['sort'] = $_SESSION['sort'];
        }
        if (isset($_SESSION['direction'])) {
            $uri['direction'] = $_SESSION['direction'];
            $this->request['direction'] = $_SESSION['direction'];
        }

        if (isset($this->request['alpha'])) {
            $key = $this->request['alpha'];
            $field = $this->request['sort'];
            $query = PlexSql::getAlphaKey($field, $key);
            if (null === $query) {
                unset($this->request['alpha']);
            } else {
                if (\is_array($query)) {
                    $this->db->Where($query[0], $query[1], $query[2]);
                } else {
                    $this->db->Where($query);
                }
            }

            if (isset($this->request['alpha'])) {
                $uri['alpha'] = $this->request['alpha'];
            }
        }

        if (isset($uri)) {
            $sql_studio = '';
            $res_array = uri_SQLQuery($uri);
            if (\array_key_exists('sort', $res_array)) {
                $order_sort = $res_array['sort'];
            }

            if (\array_key_exists('sql', $res_array)) {
                $sql_studio = $res_array['sql'];
            }

            if (isset($this->request['genre'])) {
                $where = str_replace("genre  = '".$this->request['genre']."'", 'genre like \'%'.$this->request['genre'].'%\'', $sql_studio);
            }
            if (!isset($this->request['allfiles']) && '' != $sql_studio) {
                $where = str_replace("studio = 'null'", 'studio IS NULL', $sql_studio);
            } else {
                $studio_key = '';
                $uri['allfiles'] = $this->request['allfiles'];
                $where = $sql_studio;
                $genre = '';
            }
        }

        $pageObj = new Pageinate(false, $this->currentpage, $this->urlPattern);

        foreach ($tag_array as $tag) {
            if (isset($this->request[$tag]) && '' != $this->request[$tag]) {
                $value = '%'.$this->request[$tag].'%';
                $comp = ' like';

                if ('NULL' == $this->request[$tag]) {
                    $value = null;
                    $comp = ' IS';
                }
                //  dump(['m.'.$tag, $value, $comp]);
                $tag_query = '(m.'.$tag.' '.$comp.' \''.$value.'\' OR c.'.$tag.' '.$comp.' \''.$value.'\')';
                $this->db->where($tag_query);
                // $this->db->orwhere('c.'.$tag, $value, $comp);
            }
        }

        if (isset($this->request['sort'], $this->request['direction'])) {
            $this->db->orderBy($this->request['sort'], $this->request['direction']);
        }

        $results = $this->buildSQL([$pageObj->offset, $pageObj->itemsPerPage]);

        return [$results, $pageObj, $uri];
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
        if (null === PlexSql::getLibrary()) {
            $sql .= 'm.library as library, ';
        }
        $sql .= 'i.format, i.bit_rate, i.width, i.height, ';
        $sql .= 'f.filename, f.thumbnail, f.fullpath, f.duration, f.rating, f.preview,';
        $sql .= 'f.filesize, f.added, f.id, f.video_key FROM metatags_video_file f ';
        $sql .= 'INNER JOIN metatags_video_metadata m on f.video_key=m.video_key '.PlexSql::getLibrary();
        $sql .= 'LEFT JOIN metatags_video_custom c on m.video_key=c.video_key ';
        $sql .= 'LEFT OUTER JOIN metatags_video_info i on f.video_key=i.video_key ';
        $sql .= "WHERE   f.id = '".$id."'";

        // dump($sql);
        return $this->db->query($sql);
    }

    private function loopTags($array) {}

    private function buildSQL($limit = null)
    {
        $fieldArray = ['COALESCE (c.title,m.title) as title ',
            'COALESCE (c.artist,m.artist) as artist ',
            'COALESCE (c.genre,m.genre) as genre ',
            'COALESCE (c.studio,m.studio) as studio ',
            'COALESCE (c.substudio,m.substudio) as substudio ',
            'COALESCE (c.keyword,m.keyword) as keyword '];

        $this->db->join(Db_TABLE_VIDEO_TAGS.' m', 'f.video_key=m.video_key', 'INNER');

        $this->db->join(Db_TABLE_VIDEO_CUSTOM.' c', 'f.video_key=c.video_key', 'LEFT');
        $this->db->join(Db_TABLE_VIDEO_INFO.' i', 'f.video_key=i.video_key', 'LEFT OUTER');

        if (null !== PlexSql::getLibrary()) {
            $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.library', $_SESSION['library']);
        }

        // if()
        // $fieldArray[] = 'm.library';

        $fieldArray = array_merge($fieldArray, [
            'i.format', 'i.bit_rate', 'i.width', 'i.height', 'f.library', 'f.rating',
            'f.filename', 'f.thumbnail', 'f.fullpath', 'f.preview',
            'f.duration', 'f.filesize', 'f.added', 'f.id', 'f.video_key']);
        //  $dbcount = $this->db;

        // $resultQuery  = $this->db->getQuery(
        //     Db_TABLE_VIDEO_FILE.' f'
        // );

        $num_rows = ' f.id ';

        $joinQuery = $this->db->getQuery(
            Db_TABLE_VIDEO_FILE.' f',
            null,
            $num_rows
        );

        dump($joinQuery);
        $this->saveSearch($joinQuery);
        $joinQuery = str_replace($num_rows, implode(',', $fieldArray), $joinQuery);

        if (null !== $limit) {
            $joinQuery .= ' LIMIT '.$limit[0].','.$limit[1].'';
        }
        $query = 'SELECT @rownum := @rownum + 1 AS rownum, T1.* FROM ( '.$joinQuery.' ) AS T1, (SELECT @rownum := '.$limit[0].') AS r';
        $results = $this->db->rawQuery($query);

        return $results;
    }
}
