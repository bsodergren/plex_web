<?php

/**
 * @param  $url_array
 * @return string
 */
function display_sort_options($url_array)
{
    $html        = '';
    $request_uri = '';
    $sep         = '?';
    $current = '';





    if ($url_array['query_string'] != '') {
        parse_str($url_array['query_string'], $query_parts);

        $current = 'studio';

        if (isset($url_array['direction'])) {
            $query_parts['direction'] = $url_array['direction'];
        }

        if (isset($query_parts['sort'])) {
            $current = $query_parts['sort'];
            unset($query_parts['sort']);
        }


        $request_uri = '?' . http_build_query($query_parts);
        $sep         = '&';
    }


    foreach ($url_array['sort_types'] as $key => $value) {
        $bg = '';


        if ($current == $value) {
            $bg = ' active';
        }
        $class = "btn btn-primary btn-m" . $bg;
        $request_string = $request_uri . $sep . 'sort=' . $value;

        $html          .= display_directory_navlinks($url_array['url'], $key, $request_string, $class, 'role="button" aria-pressed="true"') . "\n";
    }

    return $html;
} //end display_sort_options()


function display_directory_navlinks($url, $text, $request_uri = '', $class = '', $additional = '')
{
    global $_SESSION;
    global $_REQUEST;

    $request_string = '';

    if ($request_uri != '') {
        $request_string = $request_uri;
    }
    if ($class != '') {
        $class = " class=\"" . $class . "\"";
    }

    // $link_url = $url . "?" . $request_key ."&genre=".$_REQUEST["genre"]."&". ;
    $html = "<a href='" . $url . $request_string . "' " . $class . " " . $additional . ">" . $text . '</a>';

    return $html;
} //end display_directory_navlinks()




function display_fileInfo($fileInfoArray, $total_files)
{

    global $db;
    $table_body_html = '';
    $row_id  = $fileInfoArray['id'];
    // $row_filename = $row_id.":".$row['filename'];
    $row_filename  = $fileInfoArray['filename'];
    $row_fullpath  = $fileInfoArray['fullpath'];
    $row_video_key = $fileInfoArray['video_key'];

    if (isset($fileInfoArray['result_number'])) {
        $result_number = $fileInfoArray['result_number'];
    }

    if ($total_files >= 1) {
        $file_string = $result_number . "  of " . $total_files;
    }


    $params['FILE_NAME'] = $row_filename;
    $params['DELETE_ID']    = 'delete_' . $row_id;
    $params['FILE_NAME_ID'] = $row_id . '_filename';
    $params['FILE_NO'] = $file_string;
    $params['FULL_PATH']    = $row_fullpath;


    $params['FILE_ID'] = $row_id;

    foreach ($fileInfoArray as $key => $value) {
        $value_array = [];
        switch ($key) {
            case 'id':
                break;

            case 'filename':
                break;

            case 'fullpath':

                break;

            case 'video_key':

                break;

            case 'result_number':

                break;

            case 'library':
                break;

            case 'thumbnail':
                if (__SHOW_THUMBNAILS__ == true) {
                    $params['THUMBNAIL'] = $value;
                }
                break;
            case 'added';
                $params['ADDED_VALUE'] = $value;

                break;
            case 'duration':

                $params['DURATION'] = videoDuration($value);
                break;

            case 'favorite':
                $yeschecked = ($value == '1') ? 'checked' : '';
                $nochecked  = ($value == '0') ? 'checked' : '';

                // "PLACEHOLDER" =>  $placeholder,
                $params['YESCHECKED'] = $yeschecked;
                $params['NOCHECKED']  = $nochecked;

                $sql                 = 'select  * from ' . Db_TABLE_FILEINFO . " WHERE video_key = '" . $row_video_key . "'";
                $result              = $db->query($sql);

                $params['FULL_PATH'] = $row_fullpath;

                if (isset($result[0])) {
                    $file_info = $result[0];


                    $params['HEIGHT']   = $file_info['height'];
                    $params['WIDTH']    = $file_info['width'];
                    $params['BITRATE']  = display_size($file_info['bit_rate']);
                    $params['FILESIZE'] = byte_convert($file_info['filesize']);
                }
                break;

            default:
                $placeholder = 'placeholder="' . $value . '"';
                if ($value == '') {
                    $placeholder = '';
                    switch ($key) {
                        case 'artist':
                            $value_array = missingArtist($key, $fileInfoArray);

                            break;

                        case 'title':
                            $value_array = missingTitle($key, $fileInfoArray);

                            break;

                        case 'genre':
                            $value_array = missingGenre($key, $fileInfoArray);
                            break;

                        case 'studio':
                            $value_array = missingStudio($key, $fileInfoArray);
                            break;

                        case 'substudio':
                            $value_array = missingStudio($key, $fileInfoArray);
                            break;
                    } //end switch
                } //end if

                if ($key == "studio") {
                    $studio_value = $value;
                }

                if ($key == "substudio") {
                    if (isset($value_array[$key][0]) && $value_array[$key][0] != '') {

                        if (trim($studio_value) == trim($value_array[$key][0])) {
                            unset($value_array[$key]);
                            unset($value_array['style']);
                        }
                    }
                }

                /*  if ($value != '') {
                    $value = ' value="' . $value . '"';
                }
            */
                if (isset($value_array[$key][0]) && $value_array[$key][0] != '') {
                    $value = ' value="' . $value_array[$key][0] . '"';
                    if (isset($value_array['style'][0]) && $value_array['style'][0] != '') {
                        $value = $value . ' style="' . $value_array['style'][0] . '"';
                    }
                }




                $params[strtoupper($key) . '_PLACEHOLDER'] = $placeholder;
                $params[strtoupper($key) . '_VALUE']       = $value;

                unset($value_array);
                unset($value);

                #  echo  $table_body_html;
                break;
        } //end switch 



    } //end foreach
    $table_body_html = process_template("filelist/file", $params);
    return $table_body_html;
}





function display_filelist($results, $option = '', $page_array = [])
{
    global $db;
    global $hidden_fields;
    $table_html = '';
    $redirect_string = '';
    $total_files = '';

    if (isset($page_array['total_files'])) {
        $total_files = $page_array['total_files'];
    }

    if (isset($page_array['redirect_string'])) {
        $redirect_string = $page_array['redirect_string'];
    }


    foreach ($results as $id => $row) {

        $row_id  = $row['id'];

        $table_body = display_fileInfo($row, $total_files);
        $table_html .= process_template("filelist/file_form_wrapper",  [
            'FILE_TABLE'   => $table_body,
            'REDIRECT_STRING' => $redirect_string,
            'SUBMIT_ID' => 'hiddenSubmit_' . $row_id,
            'HIDDEN_INPUT' => $hidden_fields,
        ]);
    } //end foreach


    echo $table_html;
} //end display_filelist()


function display_navbar_left_links($url, $text, $js = '')
{
    global $_SESSION;
    $style = '';

    if ($text == $_SESSION['library']) {
        $style = ' style="background:#778899"';
    }

    $array = [
        'MENULINK_URL'  => $url,
        'MENULINK_JS'   => $style,
        'MENULINK_TEXT' => $text,
    ];
    return process_template('navbar/library_links', $array);
} //end display_navbar_left_links()


function display_navbar_links()
{
    global $navigation_link_array;
    global $_SERVER;
    $html          = '';
    $dropdown_html = '';

    foreach ($navigation_link_array as $name => $link_array) {
        if ($name == 'dropdown') {
            $dropdown_html = '';

            foreach ($link_array as $dropdown_name => $dropdown_array) {
                $dropdown_link_html = '';

                foreach ($dropdown_array as $d_name => $d_values) {
                    $array               = [
                        'DROPDOWN_URL_TEXT' => $d_name,
                        'DROPDOWN_URL'      => $d_values,
                    ];
                    $dropdown_link_html .= process_template('menu_dropdown_link', $array);
                }

                $array = [
                    'DROPDOWN_TEXT'  => $dropdown_name,
                    'DROPDOWN_LINKS' => $dropdown_link_html,
                ];

                $dropdown_html .= process_template('menu_dropdown', $array);
            }
        } else {
            $array    = [
                'MENULINK_URL'  => $link_array['url'],
                'MENULINK_JS'   => $link_array['js'],
                'MENULINK_TEXT' => $link_array['text'],
            ];
            $url_text = process_template('menu_link', $array);

            if ($link_array['secure'] == true && $_SERVER['REMOTE_USER'] != 'bjorn') {
                $html = $html . $url_text . "\n";
            } else {
                $html = $html . $url_text . "\n";
            }
        } //end if
    } //end foreach

    return $html . $dropdown_html;
} //end display_navbar_links()


function display_log($string)
{
    echo '<pre>' . $string . "</pre>\n";
} //end display_log()



function display_breadcrumbs()
{
    global $_SESSION;
    global $_REQUEST;
    global $_SERVER;

    echo '<nav style="--bs-breadcrumb-divider: \'>\';" aria-label="breadcrumb">';
    echo '<ol class="breadcrumb">';

    foreach (BREADCRUMB as $text => $url) {
        if ($text == '') {
            continue;
        }
        $class = '';
        $link = '<a href="' . $url . '">' . $text . '</a>';
        if ($url == '') {
            $class = ' active" aria-current="page';
            $link = $text;
        }
        echo '<li class="breadcrumb-item' . $class . '">' . $link . '</li>';
    }
    echo '</ol>';
    echo '</nav>';
}



function display_SelectOptions($array, $selected = '')
{

    $html = '';
    foreach ($array as $val) {

        $checked = '';
        if ($val == $selected) {
            $checked = ' selected';
        }
        $html .= '<option value="' . $val . '" ' . $checked . '>' . $val . '</option>' . "\n";
    }

    return $html;
}

function hidden_Field($name, $value)
{
    return '<input type="hidden" name="' . $name . '" value="' . $value . '">' . "\n";
}
