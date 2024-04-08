<?php

namespace Plex\Modules\Database;

use Plex\Core\Request;
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
         $this->db = PlexSql::$DB;
        // Utmdd($this->db->getlastquery());
        $this->currentpage = $currentpage;
        $this->request = $this->ReqObj->http_request;
        $this->urlPattern = $urlPattern;
    }

    public function saveSearch($query)
    {
        $db = PlexSql::$DB;
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
            self::$searchId = $id;

            return $id;
        }

        return 0;
    }

    public function getSearchResults($field, $query)
    {
        if (!\is_array($query)) {
            $query = [$query];
        }

        foreach ($query as $search) {
            $search = urldecode($search);
            // $WhereList[] = "{$field} like '%{$search}%'";
            $where[] = [
                'field' => $field,
                'search' => $search,
            ];
        }

        $pageObj = new Pageinate($where, $this->currentpage, $this->urlPattern);

        foreach ($query as $search) {
            $search = urldecode($search);
            $this->db->where('(m.'.$field.' like ? or c.'.$field.' like ?)', ['%'.$search.'%', '%'.$search.'%']);
        }

        $results = $this->buildSQL([$pageObj->offset, $pageObj->itemsPerPage]);

        return [$results, $pageObj];
    }

    public function getLatest()
    {
        if (isset($_SESSION['sort'])) {
            $uri['sort'] = $_SESSION['sort'];
            $this->request['sort'] = $_SESSION['sort'];
        }
        if (isset($_SESSION['direction'])) {
            $uri['direction'] = $_SESSION['direction'];
            $this->request['direction'] = $_SESSION['direction'];
        }
        if (isset($_SESSION['days'])) {
            $uri['days'] = $_SESSION['days'];
            $this->request['days'] = $_SESSION['days'];
            $days =  $_SESSION['days'];
        }

        if (isset($uri)) {
            $uri['allfiles'] = $this->request['allfiles'];
       }

        $pageObj = new Pageinate(false, $this->currentpage, $this->urlPattern);

        if (isset($this->request['sort'], $this->request['direction'])) {
            $this->db->orderBy($this->request['sort'], $this->request['direction']);
        }

        $this->db->where(PlexSQL::getLastest('v.added',$days));
        $results = $this->buildSQL([$pageObj->offset, $pageObj->itemsPerPage]);

        return [$results, $pageObj, $uri];
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
                if ('studio' == $tag || 'substudio' == $tag
                || 'genre' == $tag || 'artist' == $tag || 'keyword' == $tag) {
                    $key = $tag;
                }
            }
        }

        if (!isset($key)) {
            $key = 'studio';
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

            $query = PlexSql::getAlphaKey($this->request['sort'], $this->request['alpha']);
            utmdump($query);

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
            unset($query);
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

            // if (isset($this->request['genre'])) {
            //     $key = 'genre';
            // }
            // if (isset($this->request['artist'])) {
            //     $key = 'artist';
            // }

            // $where = str_replace($key."  = '".$this->request[$key]."'", $key.' like \'%'.$this->request[$key].'%\'',
            //  $sql_studio);


            if (isset($this->request['allfiles'])) {
            //     $where = str_replace("studio = 'null'", 'studio IS NULL', $sql_studio);
            // } else {
            //     //$studio_key = '';
                $uri['allfiles'] = $this->request['allfiles'];
                $where = $sql_studio;
                $genre = '';
             }
        }

        $pageObj = new Pageinate([['field'=>$key,'search'=>$this->request[$key]]], $this->currentpage, $this->urlPattern);

        foreach ($tag_array as $tag) {
            if (isset($this->request[$tag]) && '' != $this->request[$tag]) {
                $value = '%'.$this->request[$tag].'%';
                $comp = ' like';

                if ('NULL' == $this->request[$tag]) {
                    $value = null;
                    $comp = ' IS';
                }
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

    private function loopTags($array) {}

    private function buildSQL($limit = null)
    {
        $limitQuery = '';
        // $fieldArray = VideoDb::$VideoMetaFields;

        $this->db->join(Db_TABLE_VIDEO_TAGS.' m', 'v.video_key=m.video_key', 'INNER');

        $this->db->join(Db_TABLE_VIDEO_CUSTOM.' c', 'v.video_key=c.video_key', 'LEFT');
        $this->db->join(Db_TABLE_PLAYLIST_VIDEOS.' p', 'v.id=p.playlist_video_id', 'LEFT OUTER');

        $this->db->join(Db_TABLE_VIDEO_INFO.' i', 'v.video_key=i.video_key', 'LEFT OUTER');

        if (null !== PlexSql::getLibrary()) {
            $this->db->joinWhere(Db_TABLE_VIDEO_TAGS.' m', 'm.library', $_SESSION['library']);
        }

        // if()
        // $fieldArray[] = 'm.library';

        $fieldArray = array_merge(VideoDb::$VideoMetaFields, VideoDb::$VideoInfoFields, VideoDb::$VideoFileFields);
        // ,VideoDb::$PlayListFields );
        //  $dbcount = $this->db;

        // $resultQuery  = $this->db->getQuery(
        //     Db_TABLE_VIDEO_FILE.' v'
        // );

        $num_rows = ' v.id ';

        $joinQuery = $this->db->getQuery(
            Db_TABLE_VIDEO_FILE.' v',
            null,
            $num_rows
        );
        if (null !== $limit) {
            $limitQuery .= ' LIMIT '.$limit[0].','.$limit[1].'';
        }

        if (str_contains($joinQuery, 'ORDER BY')) {
            $joinQuery = str_replace('ORDER BY ', ' GROUP BY v.id  ORDER BY ', $joinQuery);
        } else {
            $joinQuery .= ' GROUP BY v.id ';
        }

        $joinQuery .= $limitQuery;

        $this->saveSearch($joinQuery);
        $joinQuery = str_replace('SELECT   v.id','SELECT ' . implode(',', $fieldArray), $joinQuery);

        $joinQuery = str_replace('SELECT ', 'SELECT count(DISTINCT p.playlist_video_id) as totalRecords, ', $joinQuery);

        $query = 'SELECT @rownum := @rownum + 1 AS rownum, T1.* FROM ( '.$joinQuery.' ) AS T1, (SELECT @rownum := '.$limit[0].') AS r';

        $results = $this->db->rawQuery($query);

        return $results;
    }
}
