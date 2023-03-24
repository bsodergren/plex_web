<?php

function videoDuration($duration)
{
    $seconds         = round($duration / 1000);
    $hours =   round($seconds / 3600);

    $minutes =   round((float) $seconds / 60 % 60);

    $sec =  round($seconds % 60);
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $sec);
}
function proccess_settings($redirect_url = '')
{
    global $form;
    global $_POST;
    global $db;

    // get our form values and assign them to a variable
    foreach ($_POST as $key => $value) {
        switch (true) {
            case $key == 'submit':
                break;

            case str_contains($key, 'setting_'):
                $pcs                   = explode('_', $key);
                $field                 = $pcs[1];
                $new_settiings[$field] = $value;
                break;

            case str_contains($key, '-NAME'):
                break;

            case key_exists($key, __SETTINGS__):
                $data = ['value' => $value];
                $db->where('name', $key);
                $db->update(Db_TABLE_SETTINGS, $data);
                break;

            case str_contains($key, '-ADD'):
                if (!key_exists(str_replace('-ADD', '', $key), __SETTINGS__)) {
                    if (!key_exists(str_replace('-NAME', '', $key), __SETTINGS__)) {
                        $key_name = str_replace('-ADD', '-NAME', $key);
                        if (key_exists($key_name, $_POST)) {
                            $value = $_POST[$key_name];
                            $field = str_replace('-NAME', '', $key_name);
                            $transfer_settings[$field] = [
                                'value' => $value,
                                'type'  => 'text',
                            ];
                        }
                    }
                }
                break;
        } //end switch
    } //end foreach

    if (is_array($transfer_settings)) {
        foreach ($transfer_settings as $name => $arr) {
            $id = $db->insert(Db_TABLE_SETTINGS, ['name' => $name, 'value' => $arr['value'], 'type' => $arr['type']]);
        }
    }

    if (is_array($new_settiings)) {
        if ($new_settiings['name'] != '') {
            $id = $db->insert(Db_TABLE_SETTINGS, $new_settiings);
        }
    }

    $form->printr($db->getLastError());
    // show a success message if no errors
    if ($form->ok()) {
        return $form->redirect($redirect_url);
    }
} //end proccess_settings()

function display_size($bytes, $precision = 2)
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
    return round($bytes, $precision) . '<span class="fs-0-8 bold">' . $units[$pow] . '</span>';
} //end display_size()


function byte_convert($size)
{
    // size smaller then 1kb
    if ($size < 1024) {
        return $size . ' Byte';
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
} //end byte_convert()


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
    } //end foreach


    if (count($uri_array) >= 1) {
        $uri_query['sql'] = implode(' AND ', $uri_array);
    }

    if (key_exists('sort', $request_array) && key_exists('direction', $request_array)) {
        $sort_query  = $request_array['sort'] . ' ' . $request_array['direction'];
        $uri_query['sort'] = $sort_query;
    }

    return $uri_query;
} //end uri_SQLQuery()




function urlQuerystring($input_string, $exclude = '')
{
    $query_string = '';

    if ($input_string != '') {
        parse_str($input_string, $query_parts);

        if (key_exists($exclude, $query_parts)) {
            unset($query_parts[$exclude]);
        }
        $query_string = uri_String($query_parts, '');
    }

    return $query_string;
}


function uri_String($request_array, $start = '?')
{
    foreach ($request_array as $key => $value) {
        if ($key == 'direction') {
            continue;
        }
        if (is_array($value)) {
            $string_value = $value[0];
        } else {
            $string_value = $value;
        }

        $string_value = urlencode($string_value);
        $uri_array[] = "$key=$string_value";
    }

    if (is_array($uri_array)) {
        $uri_string = implode('&', $uri_array);
        return $start . $uri_string;
    }

    return $request_array;
} //end uri_String()


function process_form($redirect_url = '')
{
    global $_POST;


    if (isset($_POST['submit'])) {
        if ($_POST['submit'] == 'GenreConfigSave') {
            return GenreConfigSave($_POST, $redirect_url);
            exit;
        }
        if ($_POST['submit'] == 'addNewEntry') {
            return addNewEntry($_POST, $redirect_url);
            exit;
        }

        if ($_POST['submit'] == 'StudioConfigSave') {
            return saveStudioConfig($_POST, $redirect_url);
            exit;
        }
        if ($_POST['submit'] == 'save') {
            return saveData($_POST, $redirect_url);
            exit;
        }

        if (str_starts_with($_POST['submit'], 'clear')) {
            return deleteEntry($_POST, $redirect_url);
            exit;
        }

        if (str_starts_with($_POST['submit'], 'delete')) {
            return deleteFile($_POST, $redirect_url);
            exit;
        }

        if (str_starts_with($_POST['submit'], 'hide')) {
            return hideEntry($_POST, $redirect_url);
            exit;
        }

        if (str_starts_with($_POST['submit'], 'Playlist')) {
            createPlaylist($_POST, $redirect_url);
            myHeader();
            exit;
        }
        if (str_starts_with($_POST['submit'], 'Move')) {
            $playlist_id = createPlaylist($_POST, $redirect_url);
            moveFiles($_POST, $playlist_id);
            exit;
        }
    } //end if

    if ($rediredirect_urlrect != '') {
        return myHeader($redirect_url, 0);
    }
} //end process_form()


function doRequest($request, $callback, $return = 0, $redirect = false)
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
} //end doRequest()



function deleteEntry($data_array, $redirect = false, $timeout = 4)
{
    global $db;
    global $_SERVER;
    if (key_exists('submit', $data_array)) {
        if ($data_array['submit'] == 'clear') {
            $id = $data_array['fileid'];
            logger('clear entry', $id);
            $db->where('id', $id);
            $user = $db->getOne(Db_TABLE_FILEDB);

            $thumbnail_file = $_SERVER['DOCUMENT_ROOT'] . $user['thumbnail'];
            chk_file($thumbnail_file, 'delete');

            $db->where('id', $id);
            $db->delete(Db_TABLE_FILEDB);
        }
    }


    if ($redirect != false) {
        return JavaRefresh($redirect, $timeout);
    }
} //end deleteEntry()


function deleteFile($data_array, $redirect = false, $timeout = 2)
{
    global $db;
    global $_SERVER;
    if (key_exists('submit', $data_array)) {
        if ($data_array['submit'] == 'delete') {
            $id = $data_array['fileid'];


            $db->where('id', $id);
            $user = $db->getOne(Db_TABLE_FILEDB);

            $thumbnail_file = $_SERVER['DOCUMENT_ROOT'] . $user['thumbnail'];
            $video_file     = $user['fullpath'] . $user['filename'];

            chk_file($thumbnail_file, 'delete');
            chk_file($video_file, 'delete');

            $db->where('id', $id);
            $db->delete(Db_TABLE_FILEDB);
        }
    } //end if

    if ($redirect != false) {
        return JavaRefresh($redirect, $timeout);
    }
} //end deleteFile()


function hideEntry($data_array, $redirect = false, $timeout = 4)
{
    global $db;

    if (key_exists('submit', $data_array)) {
        $key = $data_array['submit'];
        if (str_contains($key, '_') == true) {
            $pcs   = explode('_', $key);
            $id    = $pcs[1];
            $field = $pcs[0];
            if ($field == 'hide') {
                $sql = 'UPDATE ' . Db_TABLE_FILEDB . ' SET added = (CURRENT_TIMESTAMP - INTERVAL 3 day) WHERE id = ' . $id;
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
} //end hideEntry()


function addNewEntry($data_array, $redirect, $timeout = 0)
{
    global $db;

    if ($data_array['studio'] == '') {
        $data_array['studio'] = $data_array['name'];
    }

    $sql = "INSERT IGNORE INTO " . Db_TABLE_STUDIO . " (name, library, studio, path) VALUES";
    $sql .= " ( '" . $data_array['name'] . "',";
    $sql .= " '" . $data_array['library'] . "',";
    $sql .= " '" . $data_array['studio'] . "',";
    $sql .= " '" . $data_array['path'] . "')";

    $db->query($sql);


    if ($redirect != false) {
        return JavaRefresh($redirect, $timeout);
    }
}




function GenreConfigSave($data_array, $redirect, $timeout = 0)
{
    global $db;

    $__output = '';

    foreach ($data_array as $key => $val) {
        if (str_contains($key, '_') == true) {
            $value = trim($val);

            if ($value != '') {
                $pcs = explode('_', $key);

                $id         = $pcs[1];
                $field      = $pcs[0];
                if ($value == 'null') {
                    $set = '`' . $field . '`= NULL ';
                } else {
                    if ($field != "keep") {
                        $value = '"' . $value . '"';
                    }

                    $set = '`' . $field . '` = ' . $value;
                }


                $sql = 'UPDATE ' . Db_TABLE_GENRE . '  SET ' . $set . ' WHERE id = ' . $id;
                $db->query($sql);
            }
        }
    }
    if ($redirect != false) {
        return JavaRefresh($redirect, $timeout);
    }
}
function saveStudioConfig($data_array, $redirect, $timeout = 0)
{
    global $db;

    $__output = '';

    foreach ($data_array as $key => $val) {
        if (str_contains($key, '_') == true) {
            $value = trim($val);

            if ($value != '') {
                $pcs = explode('_', $key);

                $id         = $pcs[1];
                $field      = $pcs[0];
                $set = '`' . $field . '` = "' . $value . '"';

                if ($value == 'null') {
                    $set = '`' . $field . '`= NULL ';
                }

                $sql = 'UPDATE ' . Db_TABLE_STUDIO . '  SET ' . $set . ' WHERE id = ' . $id;
                $db->query($sql);
            }
        }
    }

    if ($redirect != false) {
        return JavaRefresh($redirect, $timeout);
    }
}




function saveData($data_array, $redirect = false, $timeout = 4)
{
    global $db;

    $__output = '';

    foreach ($data_array as $key => $val) {
        if (str_contains($key, '_') == true) {
            $value = trim($val);

            if ($value != '') {
                $pcs = explode('_', $key);

                $id         = $pcs[0];
                $field      = $pcs[1];
                $atom_field = $field;
                $atom_value = $value;

                if ($field == 'id') {
                    continue;
                }

                if ($field == 'filename') {
                    $filename = $value;
                    continue;
                }
                if ($field == 'tags') {
                    updateTags($id, $value);
                    continue;
                }

                if (isset($pcs[2])) {
                    $field .= '_' . $pcs[2];
                }

                if ($value == 'NULL') {
                    $sql = 'UPDATE ' . Db_TABLE_FILEDB . '  SET `' . $field . '` = NULL WHERE id = ' . $id;

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
                if ($db->update(Db_TABLE_FILEDB, $data)) {
                    logger('records were updated', $db->count);
                } else {
                    logger('update failed: ', $db->getLastError());
                }
            } //end if
        } //end if
    } //end foreach

    if ($redirect != false) {
        return myHeader($redirect);
    }
    return $__output;
} //end saveData()

function updateTags($id, $tags)
{
    global $db;

    $tag_array = explode(",", $tags);

    $sql = "delete from tags where file_id = " . $id;
    $db->query($sql);

    foreach ($tag_array as $tag) {
        $db->query("INSERT INTO tags (file_id, tag_name) VALUES ( " . $id . ", '" . $tag . "'); ");
    }
}

function moveFiles($data_array, $playlist_id)
{
    global $db;
    global $_SESSION;
    // $video_file     = $user['fullpath'] . $user['filename'];
    $sql = "select f.fullpath, f.filename from metatags_filedb as f, playlists as p where (p.playlist_id = " . $playlist_id . " and p.playlist_videos = f.id);";
    $results = $db->query($sql);


    foreach ($results as $_ => $row) {
        print_r2($row);
    }
    exit;
}


function createPlaylist($data_array, $redirect = false, $timeout = 0)
{
    global $db;
    global $_SESSION;
    $sql = 'select max(playlist_id) as playlist_id from playlists';
    $res = $db->rawQueryOne($sql);
    $playlist_id = $res['playlist_id'];
    if ($playlist_id === null) {
        $playlist_id = 0;
    } else {
        $playlist_id++;
    }


    foreach ($data_array["playlist"] as $_ => $id) {
        $data = [
            'playlist_id' => $playlist_id,
            'playlist_videos' => $id,
            'playlist_name' => 'Playlist',
            'library' => $_SESSION['library'],
        ];
        $db->insert(Db_TABLE_PLAYLIST, $data);
    }



    return  $playlist_id;
}

function myHeader($redirect = __URL_PATH__ . '/home.php')
{
    header('refresh:0;url=' . $redirect);
} //end myHeader()


function getBaseUrl($pathOnly = false)
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
        return $protocol . $hostName . $pathInfo['dirname'] . '/';
    }

    // return: http://localhost/myproject/
    return $protocol . $hostName . $pathInfo['dirname'] . '/';
} //end getBaseUrl()


function print_r2($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';
} //end print_r2()


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
} //end print_request()


function toint($string)
{
    $string_ret = str_replace(',', '', $string);
    return $string_ret;
} //end toint()


function array_find($needle, $haystack)
{
    foreach ($haystack as $item) {
        if (strpos($item, $needle) !== false) {
            return $item;
            break;
        }
    }
} //end array_find()


function getReferer()
{
    global $_SERVER;
    $url = $_SERVER['HTTP_REFERER'];
    $parts = parse_url($url);
    return $parts['path'];
}
