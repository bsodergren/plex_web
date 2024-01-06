<?php
/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */
function videoDuration($duration)
{
    $seconds = round($duration / 1000);
    $hours   = floor($seconds / 3600);

    $minutes = round((float) $seconds / 60 % 60);

    $sec     = round($seconds % 60);

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
            case 'submit' == $key:
                break;

            case str_contains($key, 'setting_'):
                $pcs                   = explode('_', $key);
                $field                 = $pcs[1];
                $new_settiings[$field] = $value;

                break;

            case str_contains($key, '-NAME'):
                break;

            case array_key_exists($key, __SETTINGS__):
                $data                  = ['value' => $value];
                $db->where('name', $key);
                $db->update(Db_TABLE_SETTINGS, $data);

                break;

            case str_contains($key, '-ADD'):
                if (!array_key_exists(str_replace('-ADD', '', $key), __SETTINGS__)) {
                    if (!array_key_exists(str_replace('-NAME', '', $key), __SETTINGS__)) {
                        $key_name = str_replace('-ADD', '-NAME', $key);
                        if (array_key_exists($key_name, $_POST)) {
                            $value                     = $_POST[$key_name];
                            $field                     = str_replace('-NAME', '', $key_name);
                            $transfer_settings[$field] = [
                                'value' => $value,
                                'type'  => 'text',
                            ];
                        }
                    }
                }

                break;
        } // end switch
    } // end foreach

    if (is_array($transfer_settings)) {
        foreach ($transfer_settings as $name => $arr) {
            $id = $db->insert(Db_TABLE_SETTINGS, ['name' => $name, 'value' => $arr['value'], 'type' => $arr['type']]);
        }
    }

    if (is_array($new_settiings)) {
        if ('' != $new_settiings['name']) {
            $id = $db->insert(Db_TABLE_SETTINGS, $new_settiings);
        }
    }

    if ($form->ok() === true) {
         $form->redirect($redirect_url);
    }
} // end proccess_settings()

function display_size($bytes, $precision = 2)
{
    $units = [
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
    ];
    $bytes = max($bytes, 0);
    $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow   = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision).'<span class="fs-0-8 bold">'.$units[$pow].'</span>';
} // end display_size()

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

            //            if (isset($this->request['alpha'])) {
            //      $uri['alpha'] = $this->request['alpha'];
            //   }
            // if ('' != $value) {
            //     $uri_array[] = $where_field." like '%{$value}'";
            // }
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
        if (false === matcharray($sort_types, $request_array['sort'])) {
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
        return JavaRefresh($redirect, $timeout);
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
        return JavaRefresh($redirect, $timeout);
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
        return JavaRefresh($redirect, $timeout);
    }
}

function myHeader($redirect = __URL_PATH__.'/home.php', $timeout = 0)
{
    header('refresh:0;url='.$redirect);
} // end myHeader()

function print_r2($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';
} // end print_r2()

function getReferer()
{
    global $_SERVER;
    $url   = $_SERVER['HTTP_REFERER'];
    $parts = parse_url($url);

    return $parts['path'];
}
