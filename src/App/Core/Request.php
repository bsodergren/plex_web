<?php

namespace Plex\Core;

use Plex\Core\Utilities\PlexArray;
use Plex\Modules\Database\PlexSql;

class Request
{
    public $http_request = [];

    public $sort_type_map = [
        'sort_types' => [
            'Studio' => 'm.studio',
            'Sub Studio' => 'm.substudio',
            'Title' => 'm.title',
            'Genre' => 'm.genre',

            'Artist' => 'm.artist',
            'Filename' => 'v.filename',

            'File size' => 'v.filesize',
            'Duration' => 'v.duration',
            'Date Added' => 'v.added',
            'Rating' => 'v.rating',
            // 'Playlist' => 'p.playlist_id',
        ],
        'map' => [
            'v.rating' => 'Rating',
            'm.studio' => 'Studio',
            'm.substudio' => 'Sub Studio',
            'v.filesize' => 'File size',
            'm.artist' => 'Artist',
            'm.title' => 'Title',
            'v.filename' => 'Filename',
            'v.duration' => 'Duration',
            'v.added' => 'Date Added',
            'm.genre' => 'Genre',
            // 'p.playlist_id' => 'Playlist',
        ],
    ];
    public static $sort_types = [
        'Studio' => 'm.studio',
        'Sub Studio' => 'm.substudio',
        'Title' => 'm.title',
        'Genre' => 'm.genre',

        'Artist' => 'm.artist',
        'Filename' => 'v.filename',
        // 'Playlist' => 'p.playlist_id',
        'File size' => 'v.filesize',
        'Duration' => 'v.duration',
        'Date Added' => 'v.added',
        'Rating' => 'v.rating',
    ];
    public static $url_array = [];
    public static $tag_array = ['genre', 'artist', 'keyword'];
    public static $tag_types = ['studio', 'substudio', 'artist', 'title', 'genre'];
    public $currentPage = '';
    public $session;
    public $urlPattern;
    public $query_string;


    public $uri;
    public const SESSION_VARS =
    [
        'itemsPerPage' => '100',
        'library' => 'Studios',
        'sort' => 'v.added',
        'direction' => 'DESC',
        'days' => 1,
        // 'alpha' => '',
    ];

    public function __construct()
    {
        $query_string_no_current = '';

        foreach ($_REQUEST as $key => $value) {
            if ('' == $value || null == $value) {
                unset($_REQUEST[$key]);
            }
        }

        $this->http_request = $_REQUEST;
        $this->session = $_SESSION;

        foreach (self::SESSION_VARS as $key => $default) {
            if (!isset($_SESSION[$key])) {
                $_SESSION[$key] = $default;
            }
            if ('direction' == $key && '' == $_SESSION[$key]) {
                $_SESSION['direction'] = 'DESC';
            }

            if (isset($_REQUEST[$key])) {
                $_SESSION[$key] = $_REQUEST[$key];
                if ('direction' == $key) {
                    if ('DESC' == $_REQUEST['direction']) {
                        $_SESSION['direction'] = 'ASC';
                    } elseif ('ASC' == $_REQUEST['direction']) {
                        $_SESSION['direction'] = 'DESC';
                    } else {
                        $_SESSION['direction'] = 'DESC';
                    }
                }
            }
        }

        unset($_REQUEST['itemsPerPage']);
        if (isset($_REQUEST['alpha'])) {
            // $_REQUEST['alpha'] = '1';
            // } else {
            $this->uri['alpha'] = $_REQUEST['alpha'];
        }

        if (!isset($_REQUEST['current'])) {
            $_REQUEST['current'] = '1';
        } else {
            $this->uri['current'] = $_REQUEST['current'];
        }

        $this->currentPage = $_REQUEST['current'];
        $this->uri['current'] = $this->currentPage;

        if (isset($_REQUEST['submit'])) {
            if ('Search' == $_REQUEST['submit']) {
                $delim = ',';
                $q_str[] = 'submit=Search';
                foreach (self::$tag_array as $tag) {
                    if (isset($_REQUEST[$tag])) {
                        $fields[] = $tag;
                        $q_str[] = 'field[]='.$tag;
                        if (\is_array($_REQUEST[$tag])) {
                            foreach ($_REQUEST[$tag] as $str) {
                                $q_str[] = $tag.'[]='.$str;
                            }
                        }
                    }
                }
                $genreStr = implode('&', $q_str);
                $_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'].'&'.$genreStr.'&grp='.$_REQUEST['grp'];
                $_REQUEST['field'] = $fields;
            }
        }

        $request_key = '';
        if ('' != $_SERVER['QUERY_STRING']) {
            $this->query_string = '&'.urlQuerystring($_SERVER['QUERY_STRING'], ['itemsPerPage']);
            $request_string_query = '?'.urlQuerystring($_SERVER['QUERY_STRING'], ['itemsPerPage']);
            $query_string_no_current ='&' . urlQuerystring($_SERVER['QUERY_STRING'], ['current']);
           // utmdd($query_string_no_current);

            $query_string_no_current = '&'.urlQuerystring($query_string_no_current, ['itemsPerPage']);
            // utmdd([$_SERVER['QUERY_STRING'],$query_string_no_current]);
        }

        $this->urlPattern = $_SERVER['PHP_SELF'].'?current=(:num)'.$query_string_no_current;
        // $this->uri = $uri;
    }

    public function getURI()
    {
        return $this->uri;
    }

    public function geturlPattern()
    {
        return $this->urlPattern;
    }

    public function url_array($url_array = false)
    {
        if (false === $url_array) {
            self::$url_array = [
                'url' => $_SERVER['SCRIPT_NAME'],
                'sortDefault' => 'm.title',
                'query_string' => $this->query_string,
                'current' => $_SESSION['sort'],
                'direction' => $_SESSION['direction'],
                'sort_types' => self::$sort_types,
                'days' => $_SESSION['days'],
            ];
        } else {
            self::$url_array = $url_array;
        }

        return self::$url_array;
    }

    public static function uri_SQLQuery($request_array)
    {
        global $sort_types;

        $uri_array = [];
        $uri_query = [];
        foreach ($request_array as $key => $value) {
            if ('sort' == $key) {
                $where_field = $value;
                continue;
            }

            if ('direction' == $key) {
                continue;
            }
            if (
                'genre' == $key
                || 'keyword' == $key
                || 'artist' == $key
            ) {
                $uri_array[] = $key." like '%{$value}%'";
                //            utmdd($key,$value);
                continue;
            }

            if ('current' == $key) {
                continue;
            }
            if ('alpha' == $key) {
                $query = PlexSql::getAlphaKey($request_array['sort'], $value);
                if (null === $query) {
                    unset($request_array['alpha']);
                } else {
                    $uri_array[] = $query;
                }

                continue;
            }
            $string_value = $value;
            if (\is_array($value)) {
                $string_value = $value[0];
            }
            $query_string = "= '{$string_value}'";
            if ('NULL' == $string_value) {
                $query_string = 'IS NULL';
            }
            // exit;
            $uri_array[] = "{$key} {$query_string}";
        } // end foreach

        if (\count($uri_array) >= 1) {
            $uri_query['sql'] = implode(' AND ', $uri_array);
        }

        if (
            \array_key_exists('sort', $request_array)
            && \array_key_exists('direction', $request_array)
        ) {
            if (false === PlexArray::matcharray(self::$sort_types, $request_array['sort'])) {
                $_SESSION['sort'] = 'm.title';
                $request_array['sort'] = 'm.title';
            }

            $sort_query = $request_array['sort'].' '.$request_array['direction'];
            $uri_query['sort'] = $sort_query;
        }

        return $uri_query;
    } // end uri_SQLQuery()

    public static function urlQuerystring($input_string, $exclude = [], $query = false)
    {
        $query_string = '';
        $parts = [];
        if ('' != $input_string) {
            parse_str($input_string, $query_parts);
            foreach ($query_parts as $field => $value) {
                if ('' != $value) {
                    $parts[$field] = $value;
                }
            }
            if (\is_array($parts)) {
                if (\is_array($exclude)) {
                    foreach ($exclude as $x) {
                        if (\array_key_exists($x, $parts)) {
                            unset($parts[$x]);
                        }
                    }
                } else {
                    if (\array_key_exists($exclude, $parts)) {
                        unset($parts[$exclude]);
                    }
                }
            }
            if(count($parts) == 0)  {
                return '';
            }
            if (false === $query) {
                $query_string = uri_String($parts, '');

            } else {
                $parts = array_reverse($parts);
                array_pop($parts);
                //   utmdd($query_parts);

                $query_string = uri_SQLQuery($parts);
                // utmdd($query_string);
            }
        }

        return str_replace('_php', '.php', $query_string);
    }

    public static function uri_String($request_array, $start = '?')
    {
        $uri_array = [];
        foreach ($request_array as $key => $value) {
            if ('direction' == $key) {
                continue;
            }
            if (\is_array($value)) {
                // utmdd($key);
                // foreach ($value as $n => $v) {
                $uri_array[] = $key.'='.urlencode(implode(',', $value));
                // }
            } else {
                if($value !== null) {
                $uri_array[] = $key.'='.urlencode($value);
                }
            }
        }

        if (count($uri_array) > 0) {
            $uri_string = implode('&', $uri_array);

            return $start.$uri_string;
        }

        return $request_array;
    }
}
