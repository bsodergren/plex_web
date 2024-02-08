<?php

use Plex\Core\PlexSql;
use Plex\Core\Utilities\Logger;
use Plex\Core\Utilities\PlexArray;


use Nette\Utils\FileSystem;

function chk_file($value, $command = 'delete', $options = '')
{
    switch ($command) {
        case 'rename':
            if (is_file($value)) {
                if (is_file($options)) {
                    chk_file($options, 'delete');
                }

                logger("Renaming {$value} to {$options}");
                rename($value, $options);
            }

            break;

        case 'delete':
            if (is_file($value)) {
                logger("deleting {$value}");
                $e = unlink($value);
                // dd($e);
            }

            break;
    }
}// end chk_file()

function deleteFile($array)
{
    global $db;
    $id        = $array['id'];

    $videoInfo = $db->where('id', $id)->getone(Db_TABLE_VIDEO_FILE);
    $file      = $videoInfo['fullpath'].\DIRECTORY_SEPARATOR.$videoInfo['filename'];
    $thumbnail = APP_HTML_ROOT.$videoInfo['thumbnail'];

    //chk_file($file, 'delete');
    rename_file($file);
    chk_file($thumbnail, 'delete');

    $res       = $db->where('id', $id)->delete(Db_TABLE_VIDEO_FILE);
    //    $res       =  $db->where('video_key', $videoInfo['video_key'])->delete(Db_TABLE_VIDEO_INFO);
    $res       = $db->where('playlist_video_id', $id)->delete(Db_TABLE_PLAYLIST_VIDEOS);

    // echo '<script type="text/javascript">   window.opener.location.reload(true); window.close(); </script>';
}

function rename_file($file)
{

    $newFile = str_replace("XXX".DIRECTORY_SEPARATOR,"XXX".DIRECTORY_SEPARATOR."Backup".DIRECTORY_SEPARATOR,$file);
    FileSystem::rename($file, $newFile,true);

}

function logger($text, $var = '', $logfile = 'default.log')
{
    Logger::log($text, $var, $logfile);
}

function getErrorLogs()
{
    return Logger::getErrorLogs();
}

function uri_SQLQuery($request_array)
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
        if ('genre' == $key ||
        'keyword' == $key ||
        'artist' == $key  ) {
            $uri_array[] = $key." like '%{$value}%'";
//            dd($key,$value);
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

function urlQuerystring($input_string, $exclude = '', $query = false)
{
    $query_string = '';

    if ('' != $input_string) {
        parse_str($input_string, $query_parts);
        foreach ($query_parts as $field => $value) {
            if ('' != $value) {
                $parts[$field] = $value;
            }
        }
        if (is_array($parts)) {
        if (is_array($exclude)) {
            foreach ($exclude as $x) {
                if (array_key_exists($x, $parts)) {
                    unset($parts[$x]);
                }
            }
        } else {
            if (array_key_exists($exclude, $parts)) {
                unset($parts[$exclude]);
            }
        }
    }
        if (false === $query) {
            $query_string = uri_String($parts, '');
        } else {
            $parts        = array_reverse($parts);
            array_pop($parts);
            //   dd($query_parts);

            $query_string = uri_SQLQuery($parts);
            // dd($query_string);

        }
    }

    return str_replace('_php', '.php', $query_string);
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
}

