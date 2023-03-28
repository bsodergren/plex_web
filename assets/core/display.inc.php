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
    $table_body_html = [];

    $x = 0;
    $row_id  = $fileInfoArray['id'];
    // $row_filename = $row_id.":".$row['filename'];
    $row_filename  = $fileInfoArray['filename'];
    $row_fullpath  = $fileInfoArray['fullpath'];
    $row_video_key = $fileInfoArray['video_key'];

    if (isset($fileInfoArray['result_number'])) {
        $result_number = $fileInfoArray['result_number'];
    }

    if ($total_files >= 1) {
    }

    $params['FILE_NAME'] = $row_filename;
    if ($fileInfoArray['title']) {
        $params['FILE_NAME'] = $fileInfoArray['title'];
    }
    $params['DELETE_ID']    = 'delete_' . $row_id;
    $params['FILE_NAME_ID'] = $row_id . '_filename';
    $params['FULL_PATH']    = $row_fullpath;

    $params['ROW_ID'] = $result_number . " <br> of<br> " . $total_files;
    ;

    $params['FILE_ID'] = $row_id;

    foreach ($fileInfoArray as $key => $value) {
        $class = ($x % 2 == 0) ? 'blueTable-tr-even' : '';
        $value_array = [];
        switch ($key) {
            case 'id':
            case 'filename':
            case 'fullpath':
            case 'video_key':
            case 'result_number':
            case 'library':

                break;

            case 'thumbnail':
                if (__SHOW_THUMBNAILS__ == true) {
                    $params['THUMBNAIL_HTML'] .= process_template(
                        "filelist/file_thumbnail",
                        [
                            'THUMBNAIL' => $value,
                            'FILE_ID' => $row_id,
                        ]
                    );
                }
                break;

            case 'added':
                $params['FIELD_ROW_HTML'] .= process_template(
                    "filelist/file_row",
                    [
                        'FIELD' => 'Added',
                        'VALUE' => $value,
                        'ALT_CLASS' => $class
                    ]
                );

                $x++;

                break;
            case 'duration':
                $params['FIELD_ROW_HTML'] .= process_template(
                    "filelist/file_row",
                    [
                        'FIELD' => 'Duration',
                        'VALUE' => videoDuration($value),
                        'ALT_CLASS' => $class
                    ]
                );
                $x++;

                break;


            case 'favorite':
                /*
                $yeschecked = ($value == '1') ? 'checked' : '';
                $nochecked  = ($value == '0') ? 'checked' : '';

                // "PLACEHOLDER" =>  $placeholder,
                $params['YESCHECKED'] = $yeschecked;
                $params['NOCHECKED']  = $nochecked;
*/
                /*
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
                */
                break;

            case 'artist':
                if ($value != '') {
                    $value = keyword_list($key, $value);
                }

                $params['FIELD_ROW_HTML'] .= process_template(
                    "filelist/file_row",
                    [
                        'FIELD' => 'Artist',
                        'VALUE' => $value,
                        'ALT_CLASS' => $class
                    ]
                );
                $x++;

                break;

            case 'title':
                /*    $params['FIELD_ROW_HTML'] .= process_template(
                    "filelist/file_row",
                    [
                        'FIELD' => 'title',
                        'VALUE' => $value,
                        'ALT_CLASS' => "text-fg-info"
                    ]
                );
                if ($value != '') {
                    $params['FILE_NAME'] = $value;
                }
*/
                //  $x++;

                break;

            case 'genre':
                if ($value != '') {
                    $value = keyword_list($key, $value);
                }
                $params['FIELD_ROW_HTML'] .= process_template(
                    "filelist/file_row",
                    [
                        'FIELD' => 'Genre',
                        'VALUE' => $value,
                        'ALT_CLASS' => $class
                    ]
                );
                $x++;
                break;
            case 'keyword':

                /*
                if( $value != ''){
                    $value = keyword_cloud($value);
                    $params['TAG_CLOUD'] = $value;
                }
                */
                $x++;
                break;
            case 'studio':
                if ($value != '') {
                    $value = process_template(
                        "filelist/search_link",
                        [
                            'KEY' => $key,
                            'QUERY' => urlencode($value),
                            'URL_TEXT' => $value
                        ]
                    );
                }
                $params['FIELD_ROW_HTML'] .= process_template(
                    "filelist/file_row",
                    [
                        'FIELD' => 'Studio',
                        'VALUE' => $value,
                        'ALT_CLASS' => $class
                    ]
                );
                $x++;
                break;

            case 'substudio':
                if ($value != '') {
                    $value = process_template(
                        "filelist/search_link",
                        [
                            'KEY' => $key,
                            'QUERY' => urlencode($value),
                            'URL_TEXT' => $value
                        ]
                    );
                }
                $params['FIELD_ROW_HTML'] .= process_template(
                    "filelist/file_row",
                    [
                        'FIELD' => 'Studio', 'VALUE' => $value,
                        'ALT_CLASS' => $class
                    ]
                );
                $x++;

                break;
                /*
            default:
                $placeholder = 'placeholder="' . $value . '"';

              //  if ($value == '') {
                    $placeholder = '';
                    switch ($key) {
                        case 'artist':
                            $params['FIELD_ROW_HTML'] .= process_template(
                                "filelist/file_row",
                                ['FIELD' => 'Artist', 'VALUE' => $value,
                                'ALT_CLASS' => $class]);
                                $x++;

                            break;

                        case 'title':
                            $params['FIELD_ROW_HTML'] .= process_template(
                                "filelist/file_row",
                                ['FIELD' => 'title', 'VALUE' => $value,
                                'ALT_CLASS' => $class]);
                                $x++;

                            break;

                        case 'genre':
                            $params['FIELD_ROW_HTML'] .= process_template(
                                "filelist/file_row",
                                ['FIELD' => 'genre', 'VALUE' => $value,
                                'ALT_CLASS' => $class]);
                                $x++;
                                break;

                        case 'studio':
                            $params['FIELD_ROW_HTML'] .= process_template(
                                "filelist/file_row",
                                ['FIELD' => 'studio', 'VALUE' => $value,
                                'ALT_CLASS' => $class]);
                                $x++;
                                break;

                        case 'substudio':
                            $params['FIELD_ROW_HTML'] .= process_template(
                                "filelist/file_row",
                                ['FIELD' => 'substudio', 'VALUE' => $value,
                                'ALT_CLASS' => $class]);
                                $x++;

                                                          break;
                    } //end switch
                //} //end if

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



                if (isset($value_array[$key][0]) && $value_array[$key][0] != '') {
                    $value = ' value="' . $value_array[$key][0] . '"';
                    if (isset($value_array['style'][0]) && $value_array['style'][0] != '') {
                        $value = $value . ' style="' . $value_array['style'][0] . '"';
                    }
                }




               # $params[strtoupper($key) . '_PLACEHOLDER'] = $placeholder;
               # $params[strtoupper($key) . '_VALUE']       = $value;

                unset($value_array);
                unset($value);

                #  echo  $table_body_html;
                break;
                */
        } //end switch
    } //end foreach
    $table_body_html['filecards'] = process_template("filelist/file", $params);
    $tag_list = '';
    $sql = "SELECT tag_name FROM tags WHERE file_id = " . $row_id;
    $res = $db->query($sql);
    if (count($res) > 0) {
        foreach ($res as $_ => $row) {
            $tag_array[$_] = $row['tag_name'];
        }

        $tag_list = implode(",", $tag_array);
        $tag_list = str_replace(",", "','", $tag_list);
        $tag_list = "['" . $tag_list . "']";

        $tag_list = " tagInput" . $row_id . ".addData(" . $tag_list . ");";
    }

    $params['TAG_DATA'] = $tag_list;
    #    $table_body_html['js'] = process_template("filelist/tag_js", $params);

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
        $js_html .= $table_body['js'];
        $table_html .= process_template("filelist/file_form_wrapper", [
            'FILE_ID' => $row_id,
            'FILE_TABLE'   => $table_body['filecards'],
            'REDIRECT_STRING' => $redirect_string,
            'SUBMIT_ID' => 'hiddenSubmit_' . $row_id,
            'HIDDEN_INPUT' => $hidden_fields,
        ]);
    } //end foreach

    $javascript_html = process_template(
        "filelist/list_js",
        [
            '__LAYOUT_URL__' => __LAYOUT_URL__,
            'JS_TAG_HTML' => $js_html,
        ]
    );

    return $table_html . $javascript_html;
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


    $crumbs_html = '';
    foreach (BREADCRUMB as $text => $url) {
        if ($text == '') {
            continue;
        }


        $class = 'breadcrumb-item';
        $link = '<a href="' . $url . '">' . $text . '</a>';

        if ($url == '') {
            $class .= ' active" aria-current="page';
            $link = $text;
        }

        $params['CLASS'] = $class;
        $params['LINK'] = $link;
        $crumbs_html .= process_template('navbar/crumb', $params);
    }


    return process_template('navbar/breadcrumb', ['CRUMB_LINKS' => $crumbs_html]);
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
