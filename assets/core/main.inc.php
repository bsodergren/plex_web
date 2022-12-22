<?php


function display_size($bytes, $precision=2)
{
    $units  = [
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
    ];
    $bytes  = max($bytes, 0);
    $pow    = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow    = min($pow, (count($units) - 1));
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision).'<span class="fs-0-8 bold">'.$units[$pow].'</span>';

}//end display_size()


function byte_convert($size)
{
    // size smaller then 1kb
    if ($size < 1024) {
        return $size.' Byte';
    }

    // size smaller then 1mb
    if ($size < 1048576) {
        return sprintf('%4.2f KB', ($size / 1024));
    }

    // size smaller then 1gb
    if ($size < 1073741824) {
        return sprintf('%4.2f MB', ($size / 1048576));
    }

    // size smaller then 1tb
    if ($size < 1099511627776) {
        return sprintf('%4.2f GB', ($size / 1073741824));
    }
    // size larger then 1tb
    else {
        return sprintf('%4.2f TB', ($size / 1073741824));
    }

}//end byte_convert()


function uri_SQLQuery($request_array)
{
    $uri_array = [];
    foreach ($request_array as $key => $value) {
        if ($key == 'sort') {
            continue;
        }

        if ($key == 'direction') {
            continue;
        }

        if ($key == 'current') {
            continue;
        }

        $string_value = $value;
        if (is_array($value)) {
            $string_value = $value[1];
        }

        $query_string = "= '$string_value'";
        if ($string_value == 'NULL') {
            $query_string = 'IS NULL';
        }
       // exit;
        $uri_array[] = "$key $query_string";
    }//end foreach


    if (count($uri_array) >= 1 ) {
        $uri_query['sql'] = implode(' AND ', $uri_array);
    }

    if (key_exists('sort', $request_array) && key_exists('direction', $request_array)) {
        $sort_query  = $request_array['sort'].' '.$request_array['direction'];
        $uri_query['sort'] = $sort_query;
    }

    return $uri_query;

}//end uri_SQLQuery()




function urlQuerystring($input_string,$exclude='')
{
    global $_SERVER;
    $query_string='';

    if (isset($input_string)) {

        parse_str($input_string, $query_parts);

        if (key_exists($exclude, $query_parts)) {
            unset($query_parts[$exclude]);
        }

        $query_string = http_build_query($query_parts);
    }

    return $query_string;

}


function uri_String($request_array)
{
    foreach ($request_array as $key => $value) {
        if (is_array($value)) {
            $string_value = $value[0];
        } else {
            $string_value = $value;
        }

        $uri_array[] = "$key=$string_value";
    }

    $uri_string = implode('&', $uri_array);
    return '?'.$uri_string;

}//end uri_String()


function process_form($redirect_url='')
{
    global $_POST;

    
    if (isset($_POST['submit'])) {
        if ($_POST['submit'] == 'save') {
            return saveData($_POST, $redirect_url);
            exit;
        }

        if (str_starts_with($_POST['submit'], 'delete')) {
            return deleteEntry($_POST, $redirect_url);
            exit;
        }

        if (str_starts_with($_POST['submit'], 'filedelete')) {
            return deleteFile($_POST, $redirect_url);
            exit;
        }

        if (str_starts_with($_POST['submit'], 'hide')) {
            return hideEntry($_POST, $redirect_url);
            exit;
        }
    }//end if

}//end process_form()


function doRequest($request, $callback, $return=0, $redirect=false)
{
    global $_REQUEST;

    $arr = array_keys($_REQUEST, $request, true);

    if (count($arr) > 0) {
        $request = $arr[0];
    }

    if (isset($_REQUEST[$request])) {
        return $callback($_REQUEST, $redirect);
    } else {
        return 0;
    }

}//end doRequest()



function deleteEntry($data_array, $redirect=false, $timeout=4)
{
    global $db;
    global $_SERVER;
    if (key_exists('submit', $data_array)) {
        $key = $data_array['submit'];
        if (str_contains($key, '_') == true) {
            $pcs   = explode('_', $key);
            $id    = $pcs[1];
            $field = $pcs[0];
            if ($field == 'delete') {
                logger('Delete entry', $id);
                $db->where('id', $id);
                $user = $db->getOne(Db_TABLE_FILEDB);

                $thumbnail_file = $_SERVER['DOCUMENT_ROOT'].$user['thumbnail'];
                chk_file($thumbnail_file, 'delete');

                $db->where('id', $id);
                $db->delete(Db_TABLE_FILEDB);
            }
        }
    }

    if ($redirect != false) {
        return JavaRefresh($redirect, $timeout);
    }

}//end deleteEntry()


function deleteFile($data_array, $redirect=false, $timeout=4)
{
    global $db;
    global $_SERVER;

    if (key_exists('submit', $data_array)) {
        $key = $data_array['submit'];
        if (str_contains($key, '_') == true) {
            $pcs   = explode('_', $key);
            $id    = $pcs[1];
            $field = $pcs[0];
            if ($field == 'filedelete') {
                $db->where('id', $id);
                $user = $db->getOne(Db_TABLE_FILEDB);

                $thumbnail_file = $_SERVER['DOCUMENT_ROOT'].$user['thumbnail'];
                $video_file     = $user['fullpath'].$user['filename'];

                chk_file($thumbnail_file, 'delete');
                chk_file($video_file, 'delete');

                $db->where('id', $id);
                $db->delete(Db_TABLE_FILEDB);
            }
        }
    }//end if

    if ($redirect != false) {
        return JavaRefresh($redirect, $timeout);
    }

}//end deleteFile()


function hideEntry($data_array, $redirect=false, $timeout=4)
{
    global $db;

    if (key_exists('submit', $data_array)) {
        $key = $data_array['submit'];
        if (str_contains($key, '_') == true) {
            $pcs   = explode('_', $key);
            $id    = $pcs[1];
            $field = $pcs[0];
            if ($field == 'hide') {
                $sql = 'UPDATE '.Db_TABLE_FILEDB.' SET added = (CURRENT_TIMESTAMP - INTERVAL 3 day) WHERE id = '.$id;
                logger('hide sql', $sql);

                $result = $db->query($sql);

                $db->where('id', $id);
                $db->delete(Db_TABLE_FILEDB);
            }
        }
    }

    if ($redirect != false) {
        return JavaRefresh($redirect, $timeout);
    }

}//end hideEntry()


function saveData($data_array, $redirect=false, $timeout=4)
{
    global $db;
    
    $__output = '';

    foreach ($data_array as $key => $value) {
        if (str_contains($key, '_') == true)
        {

            $value = trim($value);

            if ($value != '') {

                $pcs = explode('_', $key);

                $id         = $pcs[0];
                $field      = $pcs[1];
                $atom_field = $field;
                $atom_value = $value;

                logger("Key array pair", "$atom_field => $atom_value");

                if ($field == 'id') {
                    continue;
                }

                if ($field == 'filename') {
                    $filename = $value;
                    continue;
                }

                if (isset($pcs[2])) {
                    $field .= '_'.$pcs[2];
                }

                if ($value == 'NULL') {
                    $sql = 'UPDATE '.Db_TABLE_FILEDB.'  SET `'.$field.'` = NULL WHERE id = '.$id;

                    $db->query($sql);
                    $value = '';
                    $atom_value = $value;
                }

                if ($value != '') {
                    if ($field == 'artist') {
                        if (str_contains($value, '-') == true) {
                            $value = str_replace('-', ' ', $value);
                        }

                        if (str_contains($value, ',') == true) {
                            $value = str_replace(' ,', ',', $value);
                            $value = str_replace(', ', ',', $value);
                        }

                        $names_arr = explode(',', $value);
                        $names_list = '';

                        foreach ($names_arr as $str_name) {
                            $str_name = ucwords(strtolower($str_name));
                            $names_list = $str_name . ',' . $names_list;
                        }

                        $value = rtrim($names_list, ',');
                    }



                    if ($field == 'studio' || $field == 'substudio') {
                        if ($field == 'substudio') {
                            $studio_value = metadata_get_value($filename, 'studio');
                            $atom_field = 'studio';
                            $atom_value = "$studio_value/$value";
                        }
                    }
                }
                    $atom_value = trim($atom_value);
                    $value      = trim($value);
                    $__filename = basename($filename);
                    $__output .= "$__filename -> $atom_field = \"$atom_value\"<br/>";

                    logger("Metadata", $atom_field);
                    logger("Metadata", $atom_value);

                   metadata_write_filedata($filename, [$atom_field => $atom_value]);

                    $data = [$field => $value];
                    $db->where('id', $id);
                    $db->update(Db_TABLE_FILEDB, $data);
                    

                    logger( 'update failed: ' , $db->getLastError());

            }//end if
        }//end if
    }//end foreach

    if ($redirect != false) {
         return JavaRefresh($redirect, $timeout);
    }
    return $__output;
}//end saveData()


function myHeader($redirect=__URL_PATH__.'/home.php')
{
    header('refresh:0;url='.$redirect);

}//end myHeader()


function getBaseUrl($pathOnly=false)
{
    // output: /myproject/index.php
    $currentPath = $_SERVER['PHP_SELF'];

    // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index )
    $pathInfo = pathinfo($currentPath);

    // output: localhost
    $hostName = $_SERVER['HTTP_HOST'];

    // output: http://
    $protocol = strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 5)) == 'https://' ? 'https://' : 'http://';

    if ($pathOnly == true) {
        return $protocol.$hostName.$pathInfo['dirname'].'/';
    }

    // return: http://localhost/myproject/
    return $protocol.$hostName.$pathInfo['dirname'].'/';

}//end getBaseUrl()


function print_r2($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';

}//end print_r2()


function print_request($array)
{
    if (is_array($array)) {
        // $newarray=array();
        foreach ($array as $key => $value) {
            if ($value != '') {
                $newarray[$key] = $value;
            }
        }

        if (isset($newarray)) {
            print_r2($newarray);
        }
    }

}//end print_request()


function toint($string)
{
    $string_ret = str_replace(',', '', $string);
    return $string_ret;

}//end toint()


function array_find($needle, $haystack)
{
    foreach ($haystack as $item) {
        if (strpos($item, $needle) !== false) {
            return $item;
            break;
        }
    }

}//end array_find()


function getReferer()
{
    global $_SERVER;
    $url=$_SERVER['HTTP_REFERER'];	
    $parts=parse_url($url);
    return $parts['path'];


}