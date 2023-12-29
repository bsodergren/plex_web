<?php

use Plex\Utilities\Logger;
use Plex\Utilities\PlexArray;
use Plex\Template\HTMLDisplay;
/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */

 function logger($text, $var = '', $logfile = 'default.log')
 {
     Logger::log($text, $var, $logfile);
 }
 
 function getErrorLogs()
 {
     return Logger::getErrorLogs();
 }
 
function byte_convert($size)
{
    // size smaller then 1kb
    if ($size < 1024) {
        return $size.' Byte';
    }

    // size smaller then 1mb
    if ($size < 1048576) {
        return sprintf('%4.2f KB', $size / 1024);
    }

    // size smaller then 1gb
    if ($size < 1073741824) {
        return sprintf('%4.2f MB', $size / 1048576);
    }

    // size smaller then 1tb
    if ($size < 1099511627776) {
        return sprintf('%4.2f GB', $size / 1073741824);
    }
    // size larger then 1tb

    return sprintf('%4.2f TB', $size / 1073741824);
} // end byte_convert()

function uri_SQLQuery($request_array)
{
    global $sort_types;

    $uri_array = [];
    $uri_query = [];
    foreach ($request_array as $key => $value) {
        if ('sort' == $key) {
            continue;
        }

        if ('direction' == $key) {
            continue;
        }

        if ('current' == $key) {
            continue;
        }

        $string_value = $value;
        if (is_array($value)) {
            $string_value = $value[0];
        }
        $query_string = "= '{$string_value}'";
        if ('NULL' == $string_value) {
            $query_string = 'IS NULL';
        }
        // exit;
        $uri_array[]  = "{$key} {$query_string}";
    } // end foreach

    if (count($uri_array) >= 1) {
        $uri_query['sql'] = implode(' AND ', $uri_array);
    }

    if (array_key_exists('sort', $request_array) && array_key_exists('direction', $request_array)) {
        if (false === PlexArray::matcharray($sort_types, $request_array['sort'])) {
            $_SESSION['sort']      = 'm.title';
            $request_array['sort'] = 'm.title';
        }
        $sort_query        = $request_array['sort'].' '.$request_array['direction'];
        $uri_query['sort'] = $sort_query;
    }

    return $uri_query;
} // end uri_SQLQuery()

function urlQuerystring($input_string, $exclude = '')
{
    $query_string = '';

    if ('' != $input_string) {
        parse_str($input_string, $query_parts);

        if (array_key_exists($exclude, $query_parts)) {
            unset($query_parts[$exclude]);
        }
        $query_string = uri_String($query_parts, '');
    }

    return $query_string;
}

function uri_String($request_array, $start = '?')
{
    // dd($request_array);
    foreach ($request_array as $key => $value) {
        if ('direction' == $key) {
            continue;
        }
        if (is_array($value)) {
            // dd($key);
            // foreach ($value as $n => $v) {
            $uri_array[] = $key.'='.urlencode(implode(',', $value));
            // }
        } else {
            $uri_array[] = $key.'='.urlencode($value);
        }
    }

    if (is_array($uri_array)) {
        $uri_string = implode('&', $uri_array);

        return $start.$uri_string;
    }

    return $request_array;
} // end uri_String()

function GenreConfigSave($data_array, $redirect, $timeout = 0)
{
    global $db;

    $__output = '';

    foreach ($data_array as $key => $val) {
        if (true == str_contains($key, '_')) {
            $value = trim($val);

            if ('' != $value) {
                $pcs   = explode('_', $key);

                $id    = $pcs[1];
                $field = $pcs[0];
                if ('null' == $value) {
                    $set = '`'.$field.'`= NULL ';
                } else {
                    if ('keep' != $field) {
                        $value = '"'.$value.'"';
                    }

                    $set = '`'.$field.'` = '.$value;
                }

                $sql   = 'UPDATE '.Db_TABLE_GENRE.'  SET '.$set.' WHERE id = '.$id;
                $db->query($sql);
            }
        }
    }
    if (false != $redirect) {
        return HTMLDisplay::JavaRefresh($redirect, $timeout);
    }
}

function ArtistConfigSave($data_array, $redirect, $timeout = 0)
{
    global $db;

    $__output = '';
    foreach ($data_array as $key => $val) {
        if (true == str_contains($key, '_')) {
            $value = trim($val);

            if ('' != $value) {
                $pcs   = explode('_', $key);

                $id    = $pcs[1];
                $field = $pcs[0];
                if ('null' == $value) {
                    $set = '`'.$field.'`= NULL ';
                } else {
                    if ('hide' != $field) {
                        $value = '"'.$value.'"';
                    }

                    $set = '`'.$field.'` = '.$value;
                }

                $sql   = 'UPDATE '.Db_TABLE_ARTISTS.'  SET '.$set.' WHERE id = '.$id;
                $db->query($sql);
            }
        }
    }
    if (false != $redirect) {
        return HTMLDisplay::JavaRefresh($redirect, $timeout);
    }
}

function saveStudioConfig($data_array, $redirect, $timeout = 0)
{
    global $db;

    $__output = '';

    foreach ($data_array as $key => $val) {
        if (true == str_contains($key, '_')) {
            $value = trim($val);

            if ('' != $value) {
                $pcs   = explode('_', $key);

                $id    = $pcs[1];
                $field = $pcs[0];
                $set   = '`'.$field.'` = "'.$value.'"';

                if ('null' == $value) {
                    $set = '`'.$field.'`= NULL ';
                }

                $sql   = 'UPDATE '.Db_TABLE_STUDIO.'  SET '.$set.' WHERE id = '.$id;
                $db->query($sql);
            }
        }
    }

    if (false != $redirect) {
        return HTMLDisplay::JavaRefresh($redirect, $timeout);
    }
}
